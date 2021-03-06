<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 **/
class Omeka_Controllers_UsersFormTest extends Omeka_Test_AppTestCase
{    
    public function setUp()
    {
        parent::setUp();
        $this->adminUser = $this->_addNewUserWithRole('admin');
        $this->superUser = $this->_addNewUserWithRole('super');
        self::dbChanged(false);
    }

    public static function tearDownAfterClass()
    {
        self::dbChanged(true);
    }

    public function testSuperCanAccessForm()
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertController('users');
        $this->assertAction('edit', "Super users should be able to reach the 'edit' action for their user account.");
    }

    public static function formXPaths()
    {
        return array(
            array('//input[@id="username"][@value="adminuser"]', 
                "There should be a 'username' element on this form with a default "
                . "value."),
            array(
                '//input[@id="first_name"][@value="Admin"]',
                "There should be a 'first_name' element on this form with a default "
                . "value."),
            array(
                '//input[@id="last_name"][@value="User"]',
                "There should be a 'last_name' element on this form with a default value."),
            array(
                '//input[@id="email"][@value="admin@example.com"]',
                "There should be a 'email' element on this form with a default value."),
            array(
                '//input[@id="institution"][@value=""]',
                "There should be an 'institution' element on this form with no "
                . "default value."),
        );
    }

    public static function formQueries()
    {
        return array(
            array("form select#role", "There should be a 'role' select on this "
            . "form."),
            array('form input[name="active"]', "There should be an 'active' "
            . "element on this form."),
            array('form input[type="submit"]', "There should be a submit button on "
            . "this form."),
        );
    }

    /**
     * @dataProvider formXPaths
     */
    public function testFormXPath($xPath, $failMsg)
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertXpath($xPath, $failMsg);
    }   

    /**
     * @dataProvider formQueries
     */
    public function testFormQuery($query, $failMsg)
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertQuery($query, $failMsg);
    }
    
    public function testChangeOtherUsersAccountInfoAsSuperUser()
    {
        $expectedUsername = 'newuser' . mt_rand();
        $this->_authenticateUser($this->superUser);
        $this->request->setPost(array(
            'username' => $expectedUsername,
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'admin' . mt_rand() . '@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'admin',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $newUsername = $this->db->getTable('User')->find($this->adminUser->id)->username;
        $this->assertEquals($expectedUsername, $newUsername);
        $this->assertRedirectTo('/users/browse');
    }
    
    public function testChangeOwnUserAccountInfo()
    {
        $user = $this->superUser;
        $this->_authenticateUser($user);
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar' . mt_rand() . '@example.com',
            'institution' => 'School of Hard Knocks'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertRedirectTo('/');
        $changedUser = $this->db->getTable('User')->find($user->id);
        $this->assertEquals("newusername", $changedUser->username);
    }

    public function testGivingInvalidEmailCausesValidationError()
    {
        $this->_authenticateUser($this->superUser);
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'invalid.email',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertNotRedirect("This should not have redirected since the form submission was invalid.");
        // This error will be in a div in 1.x, 2.0 uses Zend_Form so it will be ul.errors.
        $this->assertQueryContentContains('div.error', "email address is not valid",
            "Form should contain an error message indicating that the email address provided was invalid.");
    }

    public function testCannotSetActiveFlagOrRoleFieldWithoutAdequatePermissions()
    {
        $this->_authenticateUser($this->adminUser);        
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '0'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $newAdminUser = $this->db->getTable('User')->find($this->adminUser->id);
        $this->assertEquals($newAdminUser->role, 'admin', "User role should not have been changed from admin to super.");
        $this->assertEquals($newAdminUser->active, 1, "User status should not have been changed from active to inactive.");
    }
        
    public function testCannotEverChangeSaltPasswordOrEntityIdFields()
    {
        $user = $this->adminUser;
        $this->_authenticateUser($user);
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '1',
            'entity_id' => '5000',
            'salt' => 'foobar',
            'password' => 'some-arbitrary-hash'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $changedUser = $this->db->getTable('User')->find($user->id);
        $this->assertEquals($user->entity_id, $changedUser->entity_id, 
            "Entity ID should not have changed.");
        $this->assertEquals($user->salt, $changedUser->salt, 
            "Salt should not have changed.");
        $this->assertEquals($user->password, $changedUser->password, 
            "Hashed password should not have changed.");
    }
        
    private function _addNewUserWithRole($role)
    {
        $username = $role . 'user';
        $existingUser = $this->_getUser($username);
        if ($existingUser) {
            $existingUser->delete();
            release_object($existingUser);
        }
        $newUser = new User;
        $newUser->username = $username;
        $newUser->setPassword('foobar');
        $newUser->role = $role;
        $newUser->active = 1;
        $newUser->Entity = new Entity;
        $newUser->Entity->first_name = ucwords($role);
        $newUser->Entity->last_name = 'User';
        $newUser->Entity->email = $role . '@example.com';
        $newUser->forceSave();
        return $newUser;
    }

    private function _getUser($username)
    {
        return $this->db->getTable('User')->findBySql("username = ?", array($username), true);
    }
}

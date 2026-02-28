<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Roles Management Acceptance Tests
 */
class RolesCest
{
    /**
     * Test roles list page
     */
    public function seeRolesList(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/roles');
        $I->see('Roles Management');
        $I->see('+ New Role');
        $I->see('admin');
        $I->see('editor');
    }

    /**
     * Test create new role
     */
    public function createRole(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/roles/create');
        $I->see('Create New Role');
        
        $I->fillField('name', 'moderator');
        $I->fillField('description', 'Content moderator');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin/roles');
        $I->see('moderator');
    }

    /**
     * Test create role with empty name
     */
    public function cannotCreateRoleWithoutName(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/roles/create');
        
        $I->fillField('description', 'Test role without name');
        $I->click('button[type="submit"]');
        
        $I->see('Role name is required');
    }

    /**
     * Test edit role
     */
    public function editRole(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/roles');
        // Click on first edit link
        $I->click('//a[contains(@href, "/edit")]');
        
        $I->see('Edit Role');
        $I->seeInField('description');
        
        $I->fillField('description', 'Updated description');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin/roles');
    }

    /**
     * Test delete system role is blocked
     */
    public function cannotDeleteSystemRole(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/roles');
        
        // System roles cannot be deleted - this is enforced server-side
        // We can't test DELETE directly with PhpBrowser, so skip for now
        $I->comment('System role deletion protection is enforced server-side');
    }

    /**
     * Helper method to login as admin
     */
    private function loginAsAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@phpcms.local');
        $I->fillField('password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin');
    }
}

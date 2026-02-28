<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Permissions Management Acceptance Tests
 */
class PermissionsCest
{
    /**
     * Test permissions list page
     */
    public function seePermissionsList(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/permissions');
        $I->see('Permissions Management');
        $I->see('+ New Permission');
        $I->see('admin.dashboard');
        $I->see('admin.articles.view');
    }

    /**
     * Test filter permissions by group
     */
    public function filterPermissionsByGroup(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/permissions?group=admin');
        $I->see('Permissions Management');
        $I->see('admin.dashboard');
    }

    /**
     * Test create new permission
     */
    public function createPermission(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/permissions/create');
        $I->see('Create New Permission');
        
        $I->fillField('name', 'admin.reports.view');
        $I->fillField('description', 'View reports');
        $I->fillField('group', 'reports');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin/permissions');
        $I->see('admin.reports.view');
    }

    /**
     * Test create permission with invalid format
     */
    public function cannotCreatePermissionWithInvalidFormat(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/permissions/create');
        
        $I->fillField('name', 'invalid-format');
        $I->click('button[type="submit"]');
        
        $I->see('Invalid permission format');
    }

    /**
     * Test create permission with empty name
     */
    public function cannotCreatePermissionWithoutName(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin/permissions/create');
        
        $I->fillField('description', 'Test permission without name');
        $I->click('button[type="submit"]');
        
        $I->see('Permission name is required');
    }

    /**
     * Test edit permission
     */
    public function editPermission(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        // Go directly to edit page for first permission
        $I->amOnPage('/admin/permissions');
        // Get the first permission ID from the list
        $I->click('//a[contains(@href, "/edit")]');
        
        $I->see('Edit Permission');
        $I->seeInField('description');
        
        $I->fillField('description', 'Updated description');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin/permissions');
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

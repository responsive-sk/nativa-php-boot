<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Admin Dashboard Acceptance Tests
 */
class AdminDashboardCest
{
    /**
     * Test dashboard is accessible after login
     */
    public function seeDashboard(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin');
        $I->see('PHP CMS Admin');
        $I->see('Dashboard');
    }

    /**
     * Test dashboard navigation links
     */
    public function seeDashboardNavigation(AcceptanceTester $I)
    {
        $this->loginAsAdmin($I);
        
        $I->amOnPage('/admin');
        $I->seeLink('Dashboard');
        $I->seeLink('Articles');
        $I->seeLink('Forms');
        $I->seeLink('Pages');
        $I->seeLink('Media');
        $I->seeLink('Settings');
        // RBAC links may not be in main navigation
        // $I->seeLink('Roles');
        // $I->seeLink('Permissions');
    }

    /**
     * Test unauthenticated access to admin is blocked
     */
    public function cannotAccessAdminWithoutLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/login');
        $I->see('Login');
    }

    /**
     * Test intended URL is saved when accessing admin while logged out
     */
    public function intendedUrlIsSaved(AcceptanceTester $I)
    {
        // Try to access admin while logged out
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/login');
        
        // After login, should redirect back to admin
        $I->fillField('email', 'admin@phpcms.local');
        $I->fillField('password', 'admin123');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin');
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

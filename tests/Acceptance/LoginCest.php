<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Login Acceptance Tests
 */
class LoginCest
{
    /**
     * Test login page displays correctly
     */
    public function seeLoginPage(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Login');
        $I->see('Email address');
        $I->see('Password');
        $I->seeElement('input[name="email"]');
        $I->seeElement('input[name="password"]');
        $I->seeElement('button[type="submit"]');
    }

    /**
     * Test successful login with admin credentials
     */
    public function loginAsAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        
        $I->fillField('email', 'admin@phpcms.local');
        $I->fillField('password', 'admin123');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/admin');
        $I->see('PHP CMS Admin');
        $I->see('Dashboard');
    }

    /**
     * Test login with invalid credentials
     */
    public function loginWithInvalidCredentials(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        
        $I->fillField('email', 'wrong@example.com');
        $I->fillField('password', 'wrongpassword');
        $I->click('button[type="submit"]');
        
        $I->seeCurrentUrlEquals('/login');
        $I->see('Invalid email or password');
    }

    /**
     * Test login with empty fields
     */
    public function loginWithEmptyFields(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->click('button[type="submit"]');
        
        // Browser should prevent submission or show validation
        $I->seeInCurrentUrl('/login');
    }

    /**
     * Test logout functionality
     */
    public function logout(AcceptanceTester $I)
    {
        // Login first
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@phpcms.local');
        $I->fillField('password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin');
        
        // Logout
        $I->click('Logout');
        $I->seeCurrentUrlEquals('/login');
        $I->see('Login');
    }

    /**
     * Test redirect to admin when already logged in
     */
    public function redirectWhenAlreadyLoggedIn(AcceptanceTester $I)
    {
        // Login first
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@phpcms.local');
        $I->fillField('password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin');
        
        // Try to access login page again
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/admin');
    }
}

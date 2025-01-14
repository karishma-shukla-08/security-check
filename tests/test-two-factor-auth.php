<?php

/**
 * Class TwoFactorAuthTest
 *
 * @package Security_Check
 */
class TwoFactorAuthTest extends WP_UnitTestCase {

    /**
     * Test the 2FA User Field
     */
    public function test_add_2fa_user_field() {
        $user_id = $this->factory->user->create();
        $user = get_userdata($user_id);

        ob_start();
        add_2fa_user_field($user);
        $output = ob_get_clean();

        $this->assertStringContainsString('<h3>Two-Factor Authentication</h3>', $output);
        $this->assertStringContainsString('<label for="2fa_secret">2FA Secret</label>', $output);
    }

    /**
     * Test saving 2FA User Field
     */
    public function test_save_2fa_user_field() {
        $user_id = $this->factory->user->create();

        $_POST['2fa_secret'] = 'test_2fa_secret';
        save_2fa_user_field($user_id);

        $saved_secret = get_user_meta($user_id, '2fa_secret', true);
        $this->assertEquals('test_2fa_secret', $saved_secret);
    }

    /**
     * Test verifying 2FA on login
     */
    public function test_verify_2fa_on_login() {
        $user_id = $this->factory->user->create(['user_login' => 'admin']);
        update_user_meta($user_id, '2fa_secret', 'VALID_SECRET');

        // Mock $_POST data
        $_POST['2fa_code'] = '123456';

        // Mock PHPGangsta GoogleAuthenticator
        $mock = $this->getMockBuilder('PHPGangsta_GoogleAuthenticator')
            ->setMethods(['verifyCode'])
            ->getMock();

        $mock->expects($this->once())
             ->method('verifyCode')
             ->with('VALID_SECRET', '123456', $this->anything())
             ->willReturn(true);

        // Inject the mock into the function
        global $gaInstance;
        $gaInstance = $mock;

        $result = verify_2fa_on_login(new WP_User($user_id), 'admin', 'password');

        $this->assertNotInstanceOf(WP_Error::class, $result);
    }
}

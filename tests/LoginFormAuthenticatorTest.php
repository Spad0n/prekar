<?php
namespace App\Tests;

use App\Security\LoginFormUserAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class LoginFormAuthenticatorTest extends TestCase
{
    private $urlGenerator;
    private $authenticator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authenticator = new LoginFormUserAuthenticator($this->urlGenerator);
    }

    public function testAuthenticate()
    {
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        
        $payload = new InputBag([
            'email' => 'test@example.com',
            'password' => 'password123',
            '_csrf_token' => 'test_csrf_token'
        ]);

        $request->expects($this->atLeast(1))
            ->method('getPayload')
            ->willReturn($payload);
        
        $request->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $session->expects($this->once())
            ->method('set')
            ->with(SecurityRequestAttributes::LAST_USERNAME, 'test@example.com');

        $passport = $this->authenticator->authenticate($request);
        $this->assertInstanceOf(Passport::class, $passport);
    }

    public function testOnAuthenticationSuccess()
    {
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);
        $session = $this->createMock(SessionInterface::class);

        $request->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('app_prekar_home_page')
            ->willReturn('/home');

        $session->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $response = $this->authenticator->onAuthenticationSuccess($request, $token, 'main');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/home', $response->getTargetUrl());
    }


    public function testGetLoginUrl()
    {
        $method = new \ReflectionMethod(LoginFormUserAuthenticator::class, 'getLoginUrl');
        $method->setAccessible(true);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with(LoginFormUserAuthenticator::LOGIN_ROUTE)
            ->willReturn('/login');

        $loginUrl = $method->invoke($this->authenticator, $this->createMock(Request::class));

        $this->assertEquals('/login', $loginUrl);
    }
}

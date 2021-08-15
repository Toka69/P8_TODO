<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    protected SessionInterface $session;

    public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session)
    {
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    public function handle(Request $request, AccessDeniedException $exception)
    {
        $this->session->getFlashBag()->add('error', $exception->getMessage());

        $referer = $request->headers->get('referer');

        if ($referer === null || empty($referer)) {
            return new RedirectResponse("/");
        }
        return new RedirectResponse($request->headers->get('referer'));
    }
}

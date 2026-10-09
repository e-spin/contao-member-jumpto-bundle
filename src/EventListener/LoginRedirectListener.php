<?php

/**
 * This file is part of e-spin/contao-member-jumpto-bundle.
 *
 * (c) 2026 e-spin
 *
 * @package   e-spin/contao-member-jumpto-bundle
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2026 e-spin
 * @license   LGPL-3.0-or-later
 */

declare(strict_types=1);

namespace Espin\MemberJumpToBundle\EventListener;

use Contao\CoreBundle\Routing\ContentUrlGenerator as CoreContentUrlGenerator;
use Contao\FrontendUser;
use Espin\MemberJumpToBundle\Security\TargetPageResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Sends the member to the page he has chosen when the login is complete.
 *
 * The page of the member wins over the redirect of the member group and over the target path of the login form. If
 * nothing is chosen, or the page is not available anymore, the Contao redirect stays untouched.
 *
 * @final
 */
#[AsEventListener]
class LoginRedirectListener
{
    private const FIREWALL = 'contao_frontend';

    public function __construct(
        private readonly TargetPageResolver $resolver,
        private readonly CoreContentUrlGenerator $urlGenerator,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $token = $event->getAuthenticatedToken();
        $user  = $token->getUser();

        // The two-factor token wraps the real one, the redirect has to wait until that step is done.
        if (
            self::FIREWALL !== $event->getFirewallName()
            || !$user instanceof FrontendUser
            || \method_exists($token, 'getAuthenticatedToken')
        ) {
            return;
        }

        $page = $this->resolver->find((int) $user->memberJumpToPage, $user);
        if (null === $page) {
            return;
        }

        try {
            $url = $this->urlGenerator->generate($page, [], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (ExceptionInterface $exception) {
            $this->logger->warning(
                \sprintf('The login target page %d of the member %d has no url.', $page->id, $user->id),
                ['exception' => $exception]
            );

            return;
        }

        $event->setResponse(new RedirectResponse($url));
    }
}

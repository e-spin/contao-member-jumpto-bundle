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

namespace Espin\MemberJumpToBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use Espin\MemberJumpToBundle\Model\JumpToEntry;
use Espin\MemberJumpToBundle\Model\JumpToList;
use Espin\MemberJumpToBundle\Security\TargetPageResolver;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The setting of a member in the frontend: the page to land on after the login.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @final
 */
#[AsFrontendModule(MemberJumpToController::TYPE, category: 'user', template: 'frontend_module/member_jumpto')]
class MemberJumpToController extends AbstractFrontendModuleController
{
    public const TYPE = 'member_jumpto';

    private const DOMAIN = 'contao_default';

    public function __construct(
        private readonly Security $security,
        private readonly Connection $connection,
        private readonly ContaoCsrfTokenManager $tokenManager,
        private readonly TargetPageResolver $resolver,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user instanceof FrontendUser) {
            return new Response();
        }

        $options = $this->availableEntries(JumpToList::fromStored($model->memberJumpToPages), $user);
        if ([] === $options) {
            return new Response();
        }

        $formId = self::TYPE . '_' . $model->id;
        $flash  = $this->flashBag($request);

        if ($formId === $request->request->get('FORM_SUBMIT')) {
            $this->handleSubmit($request, $user, $options, $formId);
        }

        $template->set('form_id', $formId);
        $template->set('action', $request->getRequestUri());
        $template->set('request_token', $this->tokenManager->getDefaultTokenValue());
        $template->set('options', $this->buildOptions($options, (int) $user->memberJumpToPage));
        $template->set('messages', null === $flash ? [] : $flash->get($formId));

        return $template->getResponse();
    }

    /**
     * Saves the choice and goes back to the page, so a reload does not send the form again.
     *
     * @param array<int, JumpToEntry> $options The accessible entries indexed by their page id.
     */
    private function handleSubmit(Request $request, FrontendUser $user, array $options, string $formId): void
    {
        $pageId  = (int) $request->request->get('memberJumpToPage');
        $success = isset($options[$pageId]) && $this->store((int) $user->id, $pageId);

        $this->flashBag($request)?->add(
            $formId,
            $this->translator->trans($success ? 'MSC.memberJumpToSaved' : 'MSC.memberJumpToFailed', [], self::DOMAIN)
        );

        throw new RedirectResponseException($request->getUri());
    }

    private function store(int $memberId, int $pageId): bool
    {
        try {
            $this->connection->update(
                'tl_member',
                ['memberJumpToPage' => $pageId, 'tstamp' => \time()],
                ['id' => $memberId]
            );
        } catch (DbalException $exception) {
            $this->logger->error('The login target page of a member could not be saved.', ['exception' => $exception]);

            return false;
        }

        return true;
    }

    /**
     * Leaves out the pages which are gone or which the member is not allowed to see.
     *
     * @return array<int, JumpToEntry> Indexed by the page id.
     */
    private function availableEntries(JumpToList $list, FrontendUser $user): array
    {
        $available = [];
        foreach ($list->entries() as $entry) {
            if (null !== $this->resolver->find($entry->pageId, $user)) {
                $available[$entry->pageId] = $entry;
            }
        }

        return $available;
    }

    /**
     * @param array<int, JumpToEntry> $options
     *
     * @return list<array{value: int, label: string, selected: bool}>
     */
    private function buildOptions(array $options, int $chosen): array
    {
        // The default is only the preselection for members who have not chosen yet or chose a page which is gone.
        $selected = isset($options[$chosen]) ? $chosen : $this->defaultPage($options);

        $result = [];
        foreach ($options as $pageId => $entry) {
            $result[] = [
                'value'    => $pageId,
                'label'    => $this->labelOf($entry),
                'selected' => $pageId === $selected,
            ];
        }

        return $result;
    }

    /**
     * @param array<int, JumpToEntry> $options
     */
    private function defaultPage(array $options): int
    {
        foreach ($options as $pageId => $entry) {
            if ($entry->default) {
                return $pageId;
            }
        }

        return (int) \array_key_first($options);
    }

    private function labelOf(JumpToEntry $entry): string
    {
        if ('' !== $entry->label) {
            return $entry->label;
        }

        $page = PageModel::findById($entry->pageId);

        return $page instanceof PageModel ? ($page->pageTitle ?: $page->title) : (string) $entry->pageId;
    }

    private function flashBag(Request $request): ?FlashBagInterface
    {
        $session = $request->hasSession() ? $request->getSession() : null;

        return $session instanceof FlashBagAwareSessionInterface ? $session->getFlashBag() : null;
    }
}

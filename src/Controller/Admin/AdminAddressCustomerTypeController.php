<?php
/**
 * 2007-2022 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright 2007-2022 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace cdigruttola\Module\Einvoice\Controller\Admin;

use Addresscustomertype;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use cdigruttola\Module\Einvoice\Core\Search\Filters\AddressCustomerTypeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAddressCustomerTypeController extends FrameworkBundleAdminController
{
    const INDEX_ROUTE = 'admin_address_customer_type';

    /**
     * @param Request $request
     * @param AddressCustomerTypeFilters $filters
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction(Request $request, AddressCustomerTypeFilters $filters)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $addressCustomerTypeGridFactory = $this->get('cdigruttola.module.einvoice.core.grid.factory.address_customer_type');
        $addressCustomerTypeGrid = $addressCustomerTypeGridFactory->getGrid($filters);

        return $this->render('@Modules/einvoice/views/templates/admin/index.html.twig', [
            'addressCustomerTypeGrid' => $this->presentGrid($addressCustomerTypeGrid),
            'help_link' => $this->generateSidebarLink($legacyController),
        ]);
    }

    /**
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function editAction(int $addressCustomerTypeId, Request $request)
    {
        $addressCustomerTypeForm = $this->get('cdigruttola.module.einvoice.core.form.identifiable_object.builder.address_customer_type_form_builder')->getFormFor($addressCustomerTypeId);
        $addressCustomerTypeForm->handleRequest($request);

        $orderReturnStateFormHandler = $this->get('cdigruttola.module.einvoice.core.form.identifiable_object.handler.address_customer_type_form_handler');

        try {
            $result = $orderReturnStateFormHandler->handleFor($addressCustomerTypeId, $addressCustomerTypeForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($addressCustomerTypeForm);
                }

                return $this->redirectToRoute(self::INDEX_ROUTE);
            }
        } catch (AddressCustomerTypeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@Modules/einvoice/views/templates/admin/edit.html.twig', [
            'addressCustomerTypeForm' => $addressCustomerTypeForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableAddressCustomerType' => $this->getQueryBus()->handle(new GetAddressCustomerTypeForEditing((int)$addressCustomerTypeId)),
            'contextLangId' => $this->getContextLangId(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function deleteAction($addressCustomerTypeId)
    {
        $addressCustomerType = new Addresscustomertype($addressCustomerTypeId);
        $errors = [];
        if (Addresscustomertype::checkAssociatedAddressToAddressCustomerType($addressCustomerTypeId)) {
            $errors[] = ['key' => 'Could not delete %i%, there is at least one address associated',
                'domain' => 'Modules.Einvoice.Einvoice',
                'parameters' => ['%i%' => $addressCustomerTypeId],];
        } else if (!$addressCustomerType->delete()) {
            $errors[] = ['key' => 'Could not delete %i%',
                'domain' => 'Modules.Einvoice.Einvoice',
                'parameters' => ['%i%' => $addressCustomerTypeId],];
        }

        if (0 === count($errors)) {
            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } else {
            $this->flashErrors($errors);
        }
        unset($addressCustomerType);
        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

}
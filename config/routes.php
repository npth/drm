<?php

declare(strict_types=1);

use App\Http\Action;
use App\Http\Middleware\Auth\RequireAuth;
use App\Http\Middleware\TransactionMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {

    $app->map(['GET', 'POST'], '/authorize', Action\Authorize::class);
    $app->post('/token', Action\Token::class);

    $app->group('/api', function (RouteCollectorProxy $group): void {

        $group->group('/access', function (RouteCollectorProxy $group): void {
            $group->get('/resources/list', Action\Access\Attribute\Categories::class);
            $group->get('/attributes/types', Action\Access\Attribute\AttributeTypes::class);
            $group->get('/attributes', Action\Access\Attribute\Attributes::class);
            $group->get('/actions', Action\Access\Attribute\Actions::class);

            $group->get('/roles/list', Action\Access\Role\Listing::class);
            $group->post('/roles/insert', Action\Access\Role\Insert::class);
            $group->put('/roles/update', Action\Access\Role\Update::class);
            $group->delete('/roles/delete', Action\Access\Role\Delete::class);

            $group->get('/rules/list', Action\Access\Rule\Listing::class);
            $group->post('/rules/insert', Action\Access\Rule\Insert::class);
            $group->put('/rules/update', Action\Access\Rule\Update::class);
            $group->delete('/rules/delete', Action\Access\Rule\Delete::class);

            $group->get('/users/list', Action\Auth\User\Listing::class);
            $group->post('/users/insert', Action\Auth\User\Insert::class);
            $group->put('/users/update', Action\Auth\User\Update::class);
            $group->delete('/users/delete', Action\Auth\User\Delete::class);

            $group->get('/sessions/list', Action\Auth\Session\Listing::class);
            $group->delete('/sessions/delete', Action\Auth\Session\Delete::class);
        })->add(RequireAuth::class);

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->get('/profile', Action\Auth\Profile::class);
            $group->get('/logout', Action\Auth\Logout::class);
        });

        $group->group('/views', function (RouteCollectorProxy $group): void {
            $group->post('/insert', Action\Logs\View\Insert::class);
            $group->get('/get', Action\Logs\View\GetAction::class);
        }); // ->add(RequireAuth::class) ???

        $group->group('/logs', function (RouteCollectorProxy $group): void {
            $group->get('/entities', Action\Logs\Logs\Entities::class);
            $group->get('/actions', Action\Logs\Logs\Actions::class);
            $group->post('/list', Action\Logs\Logs\Listing::class);
        })->add(RequireAuth::class);

        $group->group('/contact', function (RouteCollectorProxy $group): void {
            $group->post('/contacts/list', Action\Contact\ListAction::class);
            $group->post('/contacts/insert', Action\Contact\Insert::class);
            $group->put('/contacts/update', Action\Contact\Update::class);
            $group->delete('/contacts/delete', Action\Contact\Delete::class);
        })->add(RequireAuth::class);

        $group->group('/geography', function (RouteCollectorProxy $group): void {
            $group->group('/regions', function (RouteCollectorProxy $group): void {
                $group->get('/list', Action\Geography\Region\Listing::class);
                $group->post('/insert', Action\Geography\Region\Insert::class);
                $group->put('/update', Action\Geography\Region\Update::class);
                $group->delete('/delete', Action\Geography\Region\Delete::class);
            });
            $group->group('/macroregions', function (RouteCollectorProxy $group): void {
                $group->get('/list', Action\Geography\MacroRegion\Listing::class);
                $group->post('/insert', Action\Geography\MacroRegion\Insert::class);
                $group->put('/update', Action\Geography\MacroRegion\Update::class);
                $group->delete('/delete', Action\Geography\MacroRegion\Delete::class);
            });
            $group->group('/districts', function (RouteCollectorProxy $group): void {
                $group->get('/list', Action\Geography\District\Listing::class);
                $group->post('/insert', Action\Geography\District\Insert::class);
                $group->put('/update', Action\Geography\District\Update::class);
                $group->delete('/delete', Action\Geography\District\Delete::class);
            });
            $group->group('/points', function (RouteCollectorProxy $group): void {
                $group->get('/list', Action\Geography\Point\Listing::class);
                $group->post('/insert', Action\Geography\Point\Insert::class);
                $group->put('/update', Action\Geography\Point\Update::class);
                $group->delete('/delete', Action\Geography\Point\Delete::class);
            });
        })->add(RequireAuth::class);

        $group->group('/crm', function (RouteCollectorProxy $group): void {
            $group->get('/clients/types', Action\Crm\Client\Types::class);
            $group->post('/clients/types/insert', Action\Crm\Client\TypeInsert::class);
            $group->put('/clients/types/update', Action\Crm\Client\TypeUpdate::class);
            $group->delete('/clients/types/delete', Action\Crm\Client\TypeDelete::class);

            $group->post('/clients/list', Action\Crm\Client\ListAction::class);
            $group->post('/clients/insert', Action\Crm\Client\Insert::class)
                ->add(new TransactionMiddleware());
            $group->put('/clients/update', Action\Crm\Client\Update::class)
                ->add(new TransactionMiddleware());
            $group->delete('/clients/delete', Action\Crm\Client\Delete::class)
                ->add(new TransactionMiddleware());

            $group->get('/balance/operations/types', Action\Crm\Client\Operation\Types::class);
            $group->post('/balance/operations/types/insert', Action\Crm\Client\Operation\TypeInsert::class);
            $group->put('/balance/operations/types/update', Action\Crm\Client\Operation\TypeUpdate::class);
            $group->delete('/balance/operations/types/delete', Action\Crm\Client\Operation\TypeDelete::class);

            $group->post('/balance/operations/insert', Action\Crm\Client\BalanceInsert::class);
            $group->post('/balance/operations/list', Action\Crm\Client\Operation\ListAction::class);
            $group->post('/balance/operations/summary', Action\Crm\Client\Operation\Summary::class);

            $group->get('/contracts/types/list', Action\Crm\Contract\Types::class);
            $group->post('/contracts/types/insert', Action\Crm\Contract\TypeInsert::class);
            $group->put('/contracts/types/update', Action\Crm\Contract\TypeUpdate::class);
            $group->delete('/contracts/types/delete', Action\Crm\Contract\TypeDelete::class);

            $group->get('/tariffs/groups/list', Action\Crm\Tariff\Groups::class);
            $group->post('/tariffs/groups/insert', Action\Crm\Tariff\GroupInsert::class);
            $group->put('/tariffs/groups/update', Action\Crm\Tariff\GroupUpdate::class);
            $group->delete('/tariffs/groups/delete', Action\Crm\Tariff\GroupDelete::class);

            $group->get('/tariffs/list', Action\Crm\Tariff\Tariffs::class);
            $group->post('/tariffs/insert', Action\Crm\Tariff\TariffInsert::class);
            $group->put('/tariffs/update', Action\Crm\Tariff\TariffUpdate::class);
            $group->delete('/tariffs/delete', Action\Crm\Tariff\TariffDelete::class);

            $group->get('/tariffs/discounts/list', Action\Crm\Tariff\Discounts::class);
            $group->post('/tariffs/discounts/insert', Action\Crm\Tariff\DiscountInsert::class);
            $group->put('/tariffs/discounts/update', Action\Crm\Tariff\DiscountUpdate::class);
            $group->delete('/tariffs/discounts/delete', Action\Crm\Tariff\DiscountDelete::class);

            $group->get('/tariffs/district-discounts/list', Action\Crm\Tariff\DistrictDiscounts::class);
            $group->post('/tariffs/district-discounts/insert', Action\Crm\Tariff\DistrictDiscountInsert::class);
            $group->put('/tariffs/district-discounts/update', Action\Crm\Tariff\DistrictDiscountUpdate::class);
            $group->delete('/tariffs/district-discounts/delete', Action\Crm\Tariff\DistrictDiscountDelete::class);

            $group->get('/tariffs/promocodes/list', Action\Crm\Tariff\PromoCodes::class);
            $group->post('/tariffs/promocodes/insert', Action\Crm\Tariff\PromoCodeInsert::class);
            $group->put('/tariffs/promocodes/update', Action\Crm\Tariff\PromoCodeUpdate::class);
            $group->delete('/tariffs/promocodes/delete', Action\Crm\Tariff\PromoCodeDelete::class);

            $group->get('/tariffs/exchanges/list', Action\Crm\Tariff\Exchanges::class);
            $group->post('/tariffs/exchanges/insert', Action\Crm\Tariff\ExchangeInsert::class);
            $group->put('/tariffs/exchanges/update', Action\Crm\Tariff\ExchangeUpdate::class);
            $group->delete('/tariffs/exchanges/delete', Action\Crm\Tariff\ExchangeDelete::class);

            $group->get('/contracts/statuses/list', Action\Crm\Contract\Statuses::class);
            $group->get('/contracts/payment-statuses/list', Action\Crm\Contract\PaymentStatuses::class);
            $group->get('/contracts/payer-types/list', Action\Crm\Contract\PayerType::class);
            $group->get('/contracts/payment-methods/list', Action\Crm\Contract\PaymentMethods::class);

            $group->post('/contracts/list', Action\Crm\Contract\Contracts::class);
            $group->post('/contracts/insert', Action\Crm\Contract\ContractInsert::class);
            $group->put('/contracts/update', Action\Crm\Contract\ContractUpdate::class);
            $group->delete('/contracts/delete', Action\Crm\Contract\ContractDelete::class);

            $group->post('/contracts/accesses/list', Action\Crm\Contract\Accesses::class);
            $group->post('/contracts/accesses/add', Action\Crm\Contract\AccessAdd::class);
            $group->post('/contracts/accesses/copy', Action\Crm\Contract\AccessCopy::class);
            $group->post('/contracts/accesses/move', Action\Crm\Contract\AccessMove::class);
            $group->post('/contracts/accesses/set-active', Action\Crm\Contract\AccessSetActive::class);
            $group->post('/contracts/accesses/set-deleted', Action\Crm\Contract\AccessSetDeleted::class);
        })->add(RequireAuth::class);

        $group->group('/realty', function (RouteCollectorProxy $group): void {
            $group->get('/items/operations/list', Action\Realty\Items\Operations::class);

            $group->get('/items/notices/types/list', Action\Realty\Items\NoticeTypes::class);
            $group->post('/items/notices/types/insert', Action\Realty\Items\NoticeTypeInsert::class);
            $group->put('/items/notices/types/update', Action\Realty\Items\NoticeTypeUpdate::class);
            $group->delete('/items/notices/types/delete', Action\Realty\Items\NoticeTypeDelete::class);
        })->add(RequireAuth::class);
    });

    $app->get('/api/test', Action\TestAction::class);
    $app->get('/', Action\RedirectToMainAction::class);
};

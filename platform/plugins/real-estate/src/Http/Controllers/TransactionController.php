<?php

namespace Srapid\RealEstate\Http\Controllers;

use Auth;
use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Enums\TransactionTypeEnum;
use Srapid\RealEstate\Http\Requests\CreateTransactionRequest;
use Srapid\RealEstate\Repositories\Interfaces\AccountInterface;
use Srapid\RealEstate\Repositories\Interfaces\TransactionInterface;
use RealEstateHelper;

class TransactionController extends BaseController
{
    /**
     * @var TransactionInterface
     */
    protected $transactionRepository;

    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * TransactionController constructor.
     * @param TransactionInterface $transactionRepository
     * @param AccountInterface $accountRepository
     */
    public function __construct(TransactionInterface $transactionRepository, AccountInterface $accountRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Insert new Transaction into database
     *
     * @param $id
     * @param CreateTransactionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreate($id, CreateTransactionRequest $request, BaseHttpResponse $response)
    {
        if (!RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $account = $this->accountRepository->findOrFail($id);

        $request->merge([
            'user_id'    => Auth::user()->getKey(),
            'account_id' => $id,
        ]);

        $this->transactionRepository->createOrUpdate($request->input());

        if ($request->input('type') == TransactionTypeEnum::ADD) {
            $account->credits += $request->input('credits');
        } elseif ($request->input('type') == TransactionTypeEnum::REMOVE) {
            $credits = $account->credits - $request->input('credits');
            $account->credits = $credits > 0 ? $credits : 0;
        }

        $this->accountRepository->createOrUpdate($account);

        return $response
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
}

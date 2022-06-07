<?php

namespace HishabKitab\Payment\Interfaces;

/**
 * Interface VendorInterface
 * @package HishabKitab\Payment\Interfaces
 */
interface VendorInterface
{
    /**
     * provide list of transaction created by that vendor
     *
     * @param array $filters
     * @return array
     */
    public function findMany(array $filters = []): array;

    /**
     * return a single transaction detail information
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function findOne(array $transactionInfo);

    /**
     * Create or transaction entry on vendor api platform
     *
     * @param array $inputs
     * @return array
     */
    public function createTransaction(array $inputs = []): array;

    /**
     * return a single transaction detail information based on vendor response
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function transactionStatus(array $transactionInfo);

    /**
     * send a transaction cancel request to vendor api
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function cancelTransaction(array $transactionInfo);

    /**
     * send a transaction refund request to vendor api
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function refundTransaction(array $transactionInfo);
}

<?php

namespace App\Model;

/**
 * Class Investor
 * @package App\Model
 */
class Investor
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var float
     */
    private $amountMoneyWallet;
    /**
     * @var float
     */
    private $investedSumMoney;

    public function __construct($id, $amountMoneyWallet)
    {
        $this->id = $id;
        $this->amountMoneyWallet = $amountMoneyWallet;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getAmountMoneyWallet(): float
    {
        return $this->amountMoneyWallet;
    }

    /**
     * @param float $investedSumMoney
     * @return Investor
     */
    public function setInvestedSumMoney($investedSumMoney): Investor
    {
        if ($this->amountMoneyWallet >= $investedSumMoney) {
            $this->investedSumMoney = $investedSumMoney;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getInvestmentMoney(): float
    {
        if (null === $this->investedSumMoney) {
            return $this->amountMoneyWallet;
        }

        return $this->investedSumMoney;
    }
}
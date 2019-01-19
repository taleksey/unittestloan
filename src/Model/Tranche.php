<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 16.01.19
 * Time: 23:54
 */

namespace App\Model;


class Tranche
{
    /**
     * @var int
     */
    private $trancheId;

    /**
     * @var int
     */
    private $interestRate;
    /**
     * @var float
     */
    private $investMaximumAmountMoney;
    /**
     * @var float
     */
    private $raisedMoney = 0.00;
    /**
     * @var InvestorTranche[]
     */
    private $investorTranche = [];
    /**
     * @var int
     */
    private $monthlyInterestRate = 0;

    /**
     * Tranche constructor.
     * @param int $trancheId
     * @param float $investMaximumAmountMoney
     * @param int $monthlyInterestRate
     */
    public function __construct($trancheId, $investMaximumAmountMoney, $monthlyInterestRate)
    {
        $this->investMaximumAmountMoney = $investMaximumAmountMoney;
        $this->monthlyInterestRate = $monthlyInterestRate;
        $this->trancheId = $trancheId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->trancheId;
    }

    /**
     * @return float
     */
    public function getInvestMaximumAmountMoney(): float
    {
        return $this->investMaximumAmountMoney;
    }

    /**
     * @param Investor $investor
     * @return Tranche
     */
    public function addInvestor(Investor $investor, $dateAddInTranche): Tranche
    {
        if ($this->isAddNewInvestor($investor)) {
            $investmentMoney = $investor->getInvestmentMoney();
            $addInvestorTranche = new InvestorTranche($investor, $this, $dateAddInTranche);
            $this->investorTranche[] = $addInvestorTranche;
            $this->raisedMoney += $investmentMoney;
        }


        return $this;
    }

    /**
     * @return Investor[]
     */
    public function getInvestors(): array
    {
        return array_map(function (InvestorTranche $investorTranche){
            return $investorTranche->getInvestor();
        }, $this->investorTranche);
    }

    /**
     * @param $startDate
     * @param $finishDate
     */
    public function getInterestCalculate($startDate, $finishDate)
    {
        $result = [];
        foreach ($this->investorTranche as $investorTranche) {
            $interestCalculation = new InterestCalculation($investorTranche, $this->monthlyInterestRate, $startDate, $finishDate);
            try {
                $resultCalculate = $interestCalculation->calculate();
            } catch (\Exception $ex) {
                $resultCalculate = 0;
            }

            $result[$investorTranche->getInvestor()->getId()] = $resultCalculate;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getRaisedMoney(): float
    {
        return $this->raisedMoney;
    }

    /**
     * @param Investor $investor
     * @return bool
     */
    private function isAddNewInvestor(Investor $investor): bool
    {
        $investmentMoney = $investor->getInvestmentMoney();

        return $this->investMaximumAmountMoney >= $this->raisedMoney + $investmentMoney;
    }

}
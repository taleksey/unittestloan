<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 19.01.19
 * Time: 9:43
 */

namespace App\Model;


class InvestorTranche
{
    /**
     * @var Investor
     */
    private $investor;
    /**
     * @var Tranche
     */
    private $tranche;
    /**
     * @var string
     */
    private $dateAddInTranche;

    /**
     * InvestorTranche constructor.
     * @param Investor $investor
     * @param string $dateAddInTranche
     */
    public function __construct(Investor $investor, Tranche $tranche, $dateAddInTranche)
    {
        $this->investor = $investor;
        $this->dateAddInTranche = $dateAddInTranche;
        $this->tranche = $tranche;
    }

    /**
     * @return Investor
     */
    public function getInvestor(): Investor
    {
        return $this->investor;
    }

    /**
     * @return string
     */
    public function getDateAddInTranche(): string
    {
        return $this->dateAddInTranche;
    }

    public function getRaisedMoney()
    {
        return $this->tranche->getRaisedMoney();
    }
}
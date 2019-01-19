<?php

namespace App\Model;


class Loan
{
    private $tranches = [];

    private $startDate;

    private $finishDate;

    public function __construct($startDate, $finishDate)
    {
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
    }

    /**
     * @param Tranche $tranche
     * @return $this
     */
    public function addTranche(Tranche $tranche): Loan
    {
        if ($this->isAllowAddTranche($tranche)) {
            $this->tranches[] = $tranche;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTranches(): array
    {
        return $this->tranches;
    }

    /**
     * @param string $startDate
     * @param string $finishDate
     */
    public function getResultInterectPeriodTime($startDate, $finishDate)
    {
        $tranches = $this->getTranches();
        $result = [];
        foreach ($tranches as $tranche) {
            $result[] = $tranche->getInterestCalculate($startDate, $finishDate);
        }

        return $result;
    }

    /**
     * @param Tranche $tranche
     * @return bool
     */
    private function isAllowAddTranche(Tranche $tranche): bool
    {
        $id = $tranche->getId();

        return !in_array($id, $this->getAllTrancheId(), true);
    }

    /**
     * @return array
     */
    private function getAllTrancheId(): array
    {
        return array_map(function (Tranche $tranche){
            return $tranche->getId();
        }, $this->getTranches());
    }

}
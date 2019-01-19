<?php

namespace App\Model;


use function Couchbase\defaultDecoder;

class InterestCalculation
{
    /**
     * @var Investor
     */
    private $investorTranche;
    /**
     * @var float
     */
    private $monthlyInterestRate;
    /**
     * @var string
     */
    private $finishTimePeriod;


    public function __construct(InvestorTranche $investorTranche, $monthlyInterestRate, $startDate, $finishTimePeriod)
    {
        $this->investorTranche = $investorTranche;
        $this->monthlyInterestRate = $monthlyInterestRate;
        $this->startTimePeriod = $startDate;
        $this->finishTimePeriod = $finishTimePeriod;
    }

    /**
     * @throws \Exception
     */
    public function calculate()
    {
        $startDateTime = $this->getStartDate();
        $finishDateTime = new \DateTime($this->finishTimePeriod);

        $rate = $this->countTotalInterestRate($startDateTime, $finishDateTime);
        return round($rate * $this->investorTranche->getRaisedMoney() / 100, 2);
    }

    /**
     * @return \DateTime
     */
    private function getStartDate(): \DateTime
    {
        $dateAddInTranche = $this->investorTranche->getDateAddInTranche();
        $dateAddInTrancheDate = new \DateTime($dateAddInTranche);
        $setStartDate = new \DateTime($this->startTimePeriod);

        if ($dateAddInTrancheDate > $setStartDate) {
            return $dateAddInTrancheDate;
        }

        return $setStartDate;
    }

    /**
     * @param \DateTime $startDateTime
     * @param \DateTime $finishDateTime
     * @return int
     */
    private function countFullMonth(\DateTime $startDateTime, \DateTime $finishDateTime): int
    {
        $diff = $finishDateTime->diff($startDateTime);
        $year = (int)$diff->format('%y') * 12;
        $month = (int)$diff->format('%m');

        return $year + $month;
    }

    /**
     * @param \DateTime $startDateTime
     * @param \DateTime $finishDateTime
     * @return float
     * @throws \Exception
     */
    private function countTotalInterestRate(\DateTime $startDateTime, \DateTime $finishDateTime): float
    {
        $totalInterestRate = $this->monthlyInterestRate * $this->countFullMonth($startDateTime, $finishDateTime);
        $diff = $startDateTime->diff($finishDateTime);
        $days = $diff->format('%d');
        $lastDateMonthDateTime = $finishDateTime->sub(new \DateInterval('P1D'))->modify('last day of this month');
        $lastDateMonth = (int)$lastDateMonthDateTime->format('d');

        $percentageLastMonth = $this->monthlyInterestRate / $lastDateMonth;
        return $totalInterestRate + $percentageLastMonth * $days;
    }
}
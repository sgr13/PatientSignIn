<?php

namespace PatientBundle\Entity;

class Calendar
{
    private $day;
    private $month;
    private $year;
    private $firstDayInMonth;
    private $daysInMonth;
    private $numberOfWeeksInMonth;

    function __construct()
    {
        $date = strtotime(date("Y-m-d"));
        $this->day = date('d', $date);
        $this->month = date('m', $date);
        $this->year = date('Y', $date);
        $this->firstDayInMonth = '';
        $this->daysInMonth = '';
        $this->numberOfWeeksInMonth = '';
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getFirstDayInMonth()
    {
        return $this->firstDayInMonth;
    }

    /**
     * @param string $firstDayInMonth
     */
    public function setFirstDayInMonth($firstDayInMonth)
    {
        $this->firstDayInMonth = $firstDayInMonth;
    }

    /**
     * @return string
     */
    public function getDaysInMonth()
    {
        return $this->daysInMonth;
    }

    /**
     * @param string $daysInMonth
     */
    public function setDaysInMonth($daysInMonth)
    {
        $this->daysInMonth = $daysInMonth;
    }

    /**
     * @return string
     */
    public function getNumberOfWeeksInMonth()
    {
        return $this->numberOfWeeksInMonth;
    }

    /**
     * @param string $numberOfWeeksInMonth
     */
    public function setNumberOfWeeksInMonth($numberOfWeeksInMonth)
    {
        $this->numberOfWeeksInMonth = $numberOfWeeksInMonth;
    }


    public function showCalendar()
    {
        $firstDay = mktime(0,0,0,$this->month, 1, $this->year);
        $title = strftime('%B', $firstDay);
        $this->firstDayInMonth = date('N', $firstDay);
        $this->daysInMonth = cal_days_in_month(0, $this->month, $this->year);
        self::getNumberOfWeeks();

        echo "Dzień: " . $this->day . "<br>";
        echo "Miesiąc: " . $this->month . "<br>";
        echo "Rok: " . $this->year . "<br>";
        echo "Miesiąc: " . $title . "<br>";
        echo "Piewrwszy dzień miesiąca: " . $this->firstDayInMonth . "<br>";
        echo "Dni w miesiącu: " . $this->daysInMonth . "<br>";
        echo "Tygodni w miesiącu: " . $this->numberOfWeeksInMonth . "<br>";

    }

    public function getNumberOfWeeks()
    {
        if ($this->daysInMonth == 28 && $this->firstDayInMonth == 1) {
            $this->numberOfWeeksInMonth = 4;
        } else if (($this->daysInMonth == 31 && $this->firstDayInMonth > 5) || $this->daysInMonth == 30 && $this->firstDayInMonth > 6) {
            $this->numberOfWeeksInMonth = 6;
        } else {
            $this->numberOfWeeksInMonth = 5;
        }
    }

    public function decreaseMonth()
    {
        if ($this->month != 01) {
            $this->month = $this->month - 3;
            var_dump($this->month);
        }
    }

}
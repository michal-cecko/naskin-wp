<?php

namespace Theme\Modules\ICS;

use Carbon\Carbon;

class Ics
{
    private string $data;
    private string $name;

    public function setData(Carbon|string $start, Carbon|string $end, string $name, string $description = "", string $location = ""): void
    {
        $this->name = $name;
        $uid = uniqid();

        // Assuming $start is a valid date string
        if(!$start instanceof Carbon) {
            $startDateTime = Carbon::parse($start);
        } else {
            $startDateTime = $start;
        }

        if(!$end instanceof Carbon) {
            $endDateTime = Carbon::parse($end);
        } else {
            $endDateTime = $end;
        }

        // Check if it's currently in daylight saving time (summer time)
        if ($startDateTime->format('I')) {
            // If it's summer time, adjust for -2 hours
            $startDateTime->modify('-2 hours');
            $endDateTime->modify('-2 hours');
        } else {
            // If it's not summer time, adjust for -1 hour
            $startDateTime->modify('-1 hour');
            $endDateTime->modify('-1 hour');
        }

        $start = $startDateTime->format('Ymd\THis\Z');
        $end = $endDateTime->format('Ymd\THis\Z');

        $this->data = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:{$start}\nDTEND:{$end}\nLOCATION:{$location}\nTRANSP:OPAQUE\nSEQUENCE:0\nUID:{$uid}\nDTSTAMP:" . date("Ymd\THis\Z") . "\nSUMMARY:{$name}\nDESCRIPTION:{$description}\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";
    }

    public function save(): void
    {
        file_put_contents($this->name . ".ics", $this->data);
    }

    public function show(): void
    {
        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="' . $this->name . '.ics"');
        Header('Content-Length: ' . strlen($this->data));
        Header('Connection: close');

        echo $this->data;
    }
}
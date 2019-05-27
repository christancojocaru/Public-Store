<?php


namespace AppBundle\Service;


class ExportCSV
{
    public function exportCSV(array $data)
    {
        $fp = fopen('data.csv' , 'w+');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}
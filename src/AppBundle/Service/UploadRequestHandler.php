<?php


namespace AppBundle\Service;


use Exception as ExceptionAlias;

class UploadRequestHandler
{
    const MAX_ROWS = 100;

    /**
     * @param $filePath
     * @return array
     * @throws ExceptionAlias
     */
    public function getData($filePath)
    {
        $handle = fopen($filePath, 'r');

        while ($data = fgetcsv($handle, 1000, ',')) {
            $rowData[] = $data;
        }
        fclose($handle);

        unset($rowData[0]);
        if ( count($rowData) > self::MAX_ROWS ) {
            throw new ExceptionAlias(sprintf("Maximum limit of %s rows reached!", self::MAX_ROWS), 500);
        }

        return $rowData;
    }
}
<?php

namespace App\Library;

use Illuminate\Support\Facades\Validator;

class CSVValidator
{
    private $csvData;
    private $rules;
    private $headingRow;
    private $errors;
    private $headingKeys = [];

    public function construct()
    {}

    /**
     * @param $csvPath
     * @param $rules
     * @param string $encoding
     * @return $this
     * @throws \Exception
     */
    public function open($csvPath, $rules, $encoding = 'UTF-8')
    {
        $this->csvData = [];
        $this->setRules($rules);

        $csvData = $this->getCsvAsArray($csvPath)['formattedData'];
        $csvKeys = $this->getCsvAsArray($csvPath)['rowKeys'];

        $rulesKeys = array_keys($rules);

        sort($rulesKeys);
        sort($csvKeys);

        if ($csvKeys != $rulesKeys) {
            throw new \Exception('Invalid CSV File, please download template.');
        }

        if (empty($csvData)) {
            throw new \Exception('The CSV file is empty.');
        }

        $newCsvData = [];
        $ruleKeys = array_keys($this->rules);
        foreach ($csvData as $rowIndex => $csvValues) {
            foreach ($ruleKeys as $ruleKeyIndex) {
                $newCsvData[$rowIndex][$ruleKeyIndex] = $csvValues[$ruleKeyIndex];
            }
        }

        $this->csvData = $newCsvData;

        return $this;
    }

    /**
     * Given a File Path, convert to associate array.
     * If keyField is set then this will be used as a key else
     * it will use normal ascending indexes.
     *
     * E.g. when keyField = 'ID'
     * data in:
     * ID | Name
     * 10,Rick
     * data out:
     * [10 => ['ID' => 10, 'Name' => 'Rick']]
     *
     * E.g. when keyField = null
     * data in:
     * ID | Name
     * 10,Rick
     * data out:
     * [0 => ['ID' => 10, 'Name' => 'Rick']]
     *
     * @param string $filePath
     * @param string|null $keyField
     * @return array
     */
    public function getCsvAsArray($filePath, $keyField = null)
    {
        $rows = array_map('str_getcsv', file($filePath));
        $rowKeys = array_shift($rows);

        $formattedData = [];
        foreach ($rows as $row) {
            $associatedRowData = array_combine($rowKeys, $row);
            if (empty($keyField)) {
                $formattedData[] = $associatedRowData;
            } else {
                $formattedData[$associatedRowData[$keyField]] = $associatedRowData;
            }
        }

        return ['formattedData' => $formattedData, 'rowKeys' => $rowKeys];
    }

    public function fails()
    {
        $errors = [];
        foreach ($this->csvData as $rowIndex => $csvValues) {
            $validator = Validator::make($csvValues, $this->rules);
            if (!empty($this->headingRow)) {
                $validator->setAttributeNames($this->headingRow);
            }
            if ($validator->fails()) {
                $errors[$rowIndex] = $validator->messages()->toArray();
            }
        }
        $this->errors = $errors;

        return (!empty($this->errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->csvData;
    }

    public function setAttributeNames($attribute_names)
    {
        $this->headingRow = $attribute_names;
    }

    private function setRules($rules)
    {
        $this->rules = $rules;
        $this->headingKeys = array_keys($rules);
    }
}

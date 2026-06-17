<?php

namespace App\ML;

class DataPreprocessor
{
    private $means = [];
    private $stdevs = [];

    /**
     * Impute missing values with column means.
     */
    public function imputeMissing(&$dataset, $features = ['tavg', 'rh_avg'])
    {
        $sums = [];
        $counts = [];

        foreach ($features as $feature) {
            $sums[$feature] = 0;
            $counts[$feature] = 0;
        }

        // Calculate sums and counts of non-null values
        foreach ($dataset as $row) {
            foreach ($features as $feature) {
                if (isset($row[$feature]) && $row[$feature] !== null && $row[$feature] !== '') {
                    $sums[$feature] += (double)$row[$feature];
                    $counts[$feature]++;
                }
            }
        }

        // Compute means
        $columnMeans = [];
        foreach ($features as $feature) {
            $columnMeans[$feature] = $counts[$feature] > 0 ? $sums[$feature] / $counts[$feature] : 0;
        }

        // Fill missing values
        foreach ($dataset as &$row) {
            foreach ($features as $feature) {
                if (!isset($row[$feature]) || $row[$feature] === null || $row[$feature] === '') {
                    $row[$feature] = $columnMeans[$feature];
                } else {
                    $row[$feature] = (double)$row[$feature];
                }
            }
        }
    }

    /**
     * Split dataset into training and testing sets.
     */
    public function trainTestSplit($X, $y, $testSize = 0.2)
    {
        $count = count($X);
        $indices = range(0, $count - 1);
        shuffle($indices);

        $testCount = (int)round($count * $testSize);
        $testIndices = array_slice($indices, 0, $testCount);
        $trainIndices = array_slice($indices, $testCount);

        $X_train = [];
        $y_train = [];
        $X_test = [];
        $y_test = [];

        foreach ($trainIndices as $idx) {
            $X_train[] = $X[$idx];
            $y_train[] = $y[$idx];
        }

        foreach ($testIndices as $idx) {
            $X_test[] = $X[$idx];
            $y_test[] = $y[$idx];
        }

        return [$X_train, $X_test, $y_train, $y_test];
    }

    /**
     * Fit standardizer (compute mean and stdev) on training set.
     */
    public function fit($X)
    {
        $numSamples = count($X);
        if ($numSamples === 0) return;
        $numFeatures = count($X[0]);

        $this->means = array_fill(0, $numFeatures, 0.0);
        $this->stdevs = array_fill(0, $numFeatures, 0.0);

        // Compute means
        for ($i = 0; $i < $numSamples; $i++) {
            for ($j = 0; $j < $numFeatures; $j++) {
                $this->means[$j] += $X[$i][$j];
            }
        }
        for ($j = 0; $j < $numFeatures; $j++) {
            $this->means[$j] /= $numSamples;
        }

        // Compute standard deviation
        for ($i = 0; $i < $numSamples; $i++) {
            for ($j = 0; $j < $numFeatures; $j++) {
                $this->stdevs[$j] += pow($X[$i][$j] - $this->means[$j], 2);
            }
        }
        for ($j = 0; $j < $numFeatures; $j++) {
            $variance = $this->stdevs[$j] / $numSamples;
            $this->stdevs[$j] = sqrt($variance);
            if ($this->stdevs[$j] == 0.0) {
                $this->stdevs[$j] = 1.0; // Avoid division by zero
            }
        }
    }

    /**
     * Transform data using computed mean and stdev.
     */
    public function transform($X)
    {
        $scaledX = [];
        $numFeatures = count($X[0] ?? []);

        foreach ($X as $row) {
            $scaledRow = [];
            for ($j = 0; $j < $numFeatures; $j++) {
                $mean = $this->means[$j] ?? 0.0;
                $stdev = $this->stdevs[$j] ?? 1.0;
                $scaledRow[] = ($row[$j] - $mean) / $stdev;
            }
            $scaledX[] = $scaledRow;
        }

        return $scaledX;
    }

    /**
     * Fit and Transform in one step.
     */
    public function fitTransform($X)
    {
        $this->fit($X);
        return $this->transform($X);
    }

    public function getMeans()
    {
        return $this->means;
    }

    public function getStdevs()
    {
        return $this->stdevs;
    }

    public function setParameters($means, $stdevs)
    {
        $this->means = $means;
        $this->stdevs = $stdevs;
    }
}

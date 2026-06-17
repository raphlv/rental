<?php

namespace App\ML;

class RandomForest
{
    private $trees = [];
    private $nTrees;
    private $maxDepth;
    private $minSamplesSplit;

    public function __construct($nTrees = 10, $maxDepth = 10, $minSamplesSplit = 2)
    {
        $this->nTrees = $nTrees;
        $this->maxDepth = $maxDepth;
        $this->minSamplesSplit = $minSamplesSplit;
    }

    /**
     * Train the random forest using bootstrap sampling.
     */
    public function train($X, $y)
    {
        $this->trees = [];
        $numSamples = count($X);
        if ($numSamples === 0) return;
        $numFeatures = count($X[0]);

        // Feature subspace dimension: sqrt(M), at least 1
        $numFeaturesToConsider = (int)max(1, round(sqrt($numFeatures)));

        for ($i = 0; $i < $this->nTrees; $i++) {
            // Create bootstrap sample
            list($X_bootstrap, $y_bootstrap) = $this->getBootstrapSample($X, $y);

            // Initialize tree
            $tree = new DecisionTree($this->maxDepth, $this->minSamplesSplit, $numFeaturesToConsider);
            $tree->train($X_bootstrap, $y_bootstrap);

            $this->trees[] = $tree;
        }
    }

    /**
     * Predict class for a dataset.
     */
    public function predict($X)
    {
        $predictions = [];
        foreach ($X as $x) {
            $predictions[] = $this->predictSample($x);
        }
        return $predictions;
    }

    /**
     * Predict class for a single sample.
     */
    public function predictSample($x)
    {
        $votes = [];
        foreach ($this->trees as $tree) {
            $vote = $tree->predictSample($x);
            if (!isset($votes[$vote])) {
                $votes[$vote] = 0;
            }
            $votes[$vote]++;
        }

        if (empty($votes)) return 0;

        arsort($votes);
        return array_key_first($votes);
    }

    /**
     * Predict class probabilities/confidence for a single sample.
     * Returns an array, e.g., [0 => 0.8, 1 => 0.2]
     */
    public function predictProba($x)
    {
        $votes = [0 => 0, 1 => 0];
        $totalTrees = count($this->trees);
        if ($totalTrees === 0) return [0 => 1.0, 1 => 0.0];

        foreach ($this->trees as $tree) {
            $vote = $tree->predictSample($x);
            if (isset($votes[$vote])) {
                $votes[$vote]++;
            } else {
                $votes[$vote] = 1;
            }
        }

        $probabilities = [];
        foreach ($votes as $class => $count) {
            $probabilities[$class] = $count / $totalTrees;
        }

        return $probabilities;
    }

    /**
     * Generate bootstrap sample (sample with replacement of size N).
     */
    private function getBootstrapSample($X, $y)
    {
        $numSamples = count($X);
        $X_bootstrap = [];
        $y_bootstrap = [];

        for ($i = 0; $i < $numSamples; $i++) {
            $randomIndex = rand(0, $numSamples - 1);
            $X_bootstrap[] = $X[$randomIndex];
            $y_bootstrap[] = $y[$randomIndex];
        }

        return [$X_bootstrap, $y_bootstrap];
    }
}

<?php

namespace App\ML;

class DecisionTreeNode
{
    public $featureIndex = null;
    public $threshold = null;
    public $left = null;
    public $right = null;
    public $value = null; // Stored class value for leaf node
    public $isLeaf = false;
}

class DecisionTree
{
    private $root;
    private $maxDepth;
    private $minSamplesSplit;
    private $numFeaturesToConsider = null; // For Random Forest feature subspace sampling

    public function __construct($maxDepth = 10, $minSamplesSplit = 2, $numFeaturesToConsider = null)
    {
        $this->maxDepth = $maxDepth;
        $this->minSamplesSplit = $minSamplesSplit;
        $this->numFeaturesToConsider = $numFeaturesToConsider;
    }

    /**
     * Train the decision tree.
     */
    public function train($X, $y)
    {
        $this->root = $this->buildTree($X, $y, 0);
    }

    /**
     * Predict class for a single sample.
     */
    public function predictSample($x)
    {
        return $this->traverseTree($this->root, $x);
    }

    /**
     * Predict classes for a dataset.
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
     * Recursively build the tree.
     */
    private function buildTree($X, $y, $depth)
    {
        $numSamples = count($X);
        $numFeatures = $numSamples > 0 ? count($X[0]) : 0;

        // Check base cases
        if ($depth >= $this->maxDepth || $numSamples < $this->minSamplesSplit || $this->isPure($y)) {
            $node = new DecisionTreeNode();
            $node->isLeaf = true;
            $node->value = $this->majorityVote($y);
            return $node;
        }

        // Determine which features to check (random subset for Random Forest)
        $featureIndices = range(0, $numFeatures - 1);
        if ($this->numFeaturesToConsider !== null && $this->numFeaturesToConsider < $numFeatures) {
            shuffle($featureIndices);
            $featureIndices = array_slice($featureIndices, 0, $this->numFeaturesToConsider);
        }

        // Find the best split
        $bestSplit = $this->findBestSplit($X, $y, $featureIndices);

        // If no split was found that improves impurity
        if ($bestSplit['gain'] <= 0.0) {
            $node = new DecisionTreeNode();
            $node->isLeaf = true;
            $node->value = $this->majorityVote($y);
            return $node;
        }

        // Create child nodes
        $leftNode = $this->buildTree($bestSplit['X_left'], $bestSplit['y_left'], $depth + 1);
        $rightNode = $this->buildTree($bestSplit['X_right'], $bestSplit['y_right'], $depth + 1);

        $node = new DecisionTreeNode();
        $node->featureIndex = $bestSplit['feature_index'];
        $node->threshold = $bestSplit['threshold'];
        $node->left = $leftNode;
        $node->right = $rightNode;
        return $node;
    }

    /**
     * Traverse the tree for prediction.
     */
    private function traverseTree($node, $x)
    {
        if ($node->isLeaf) {
            return $node->value;
        }

        if ($x[$node->featureIndex] <= $node->threshold) {
            return $this->traverseTree($node->left, $x);
        } else {
            return $this->traverseTree($node->right, $x);
        }
    }

    /**
     * Check if all target values are of the same class.
     */
    private function isPure($y)
    {
        if (count($y) === 0) return true;
        $first = $y[0];
        foreach ($y as $val) {
            if ($val !== $first) return false;
        }
        return true;
    }

    /**
     * Get majority vote class.
     */
    private function majorityVote($y)
    {
        if (count($y) === 0) return 0;
        $counts = array_count_values($y);
        arsort($counts);
        return array_key_first($counts);
    }

    /**
     * Calculate Gini Impurity.
     */
    private function calculateGini($y)
    {
        $numSamples = count($y);
        if ($numSamples === 0) return 0.0;

        $counts = array_count_values($y);
        $impurity = 1.0;
        foreach ($counts as $class => $count) {
            $probability = $count / $numSamples;
            $impurity -= pow($probability, 2);
        }
        return $impurity;
    }

    /**
     * Find the best split parameters.
     */
    private function findBestSplit($X, $y, $featureIndices)
    {
        $bestSplit = [
            'gain' => -1.0,
            'feature_index' => null,
            'threshold' => null,
            'X_left' => [],
            'y_left' => [],
            'X_right' => [],
            'y_right' => []
        ];

        $currentGini = $this->calculateGini($y);
        $numSamples = count($X);

        foreach ($featureIndices as $featIndex) {
            // Get all feature values and sort them
            $values = [];
            for ($i = 0; $i < $numSamples; $i++) {
                $values[] = $X[$i][$featIndex];
            }
            $uniqueValues = array_unique($values);
            sort($uniqueValues);

            // Test midpoints as thresholds
            $numUnique = count($uniqueValues);
            if ($numUnique < 2) continue;

            for ($i = 0; $i < $numUnique - 1; $i++) {
                $threshold = ($uniqueValues[$i] + $uniqueValues[$i+1]) / 2.0;

                // Split datasets
                $X_left = [];
                $y_left = [];
                $X_right = [];
                $y_right = [];

                for ($j = 0; $j < $numSamples; $j++) {
                    if ($X[$j][$featIndex] <= $threshold) {
                        $X_left[] = $X[$j];
                        $y_left[] = $y[$j];
                    } else {
                        $X_right[] = $X[$j];
                        $y_right[] = $y[$j];
                    }
                }

                $n_left = count($y_left);
                $n_right = count($y_right);

                if ($n_left == 0 || $n_right == 0) continue;

                // Calculate weighted Gini
                $gini_left = $this->calculateGini($y_left);
                $gini_right = $this->calculateGini($y_right);
                $weightedGini = ($n_left / $numSamples) * $gini_left + ($n_right / $numSamples) * $gini_right;

                // Information gain is the reduction in impurity
                $gain = $currentGini - $weightedGini;

                if ($gain > $bestSplit['gain']) {
                    $bestSplit['gain'] = $gain;
                    $bestSplit['feature_index'] = $featIndex;
                    $bestSplit['threshold'] = $threshold;
                    $bestSplit['X_left'] = $X_left;
                    $bestSplit['y_left'] = $y_left;
                    $bestSplit['X_right'] = $X_right;
                    $bestSplit['y_right'] = $y_right;
                }
            }
        }

        return $bestSplit;
    }
}

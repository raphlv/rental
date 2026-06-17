<?php

namespace App\ML;

class SVM
{
    private $C;
    private $kernel;
    private $gamma;
    private $tol;
    private $maxPasses;

    // Model parameters
    private $b = 0.0;
    private $supportVectors = [];
    private $supportAlphas = [];
    private $supportLabels = [];

    public function __construct($C = 1.0, $kernel = 'linear', $gamma = 0.5, $tol = 0.001, $maxPasses = 10)
    {
        $this->C = (double)$C;
        $this->kernel = $kernel;
        $this->gamma = (double)$gamma;
        $this->tol = (double)$tol;
        $this->maxPasses = (int)$maxPasses;
    }

    /**
     * Compute Kernel function between two samples.
     */
    private function computeKernel($x1, $x2)
    {
        if ($this->kernel === 'linear') {
            $dot = 0.0;
            $len = count($x1);
            for ($i = 0; $i < $len; $i++) {
                $dot += $x1[$i] * $x2[$i];
            }
            return $dot;
        } elseif ($this->kernel === 'rbf') {
            $sum = 0.0;
            $len = count($x1);
            for ($i = 0; $i < $len; $i++) {
                $sum += pow($x1[$i] - $x2[$i], 2);
            }
            return exp(-$this->gamma * $sum);
        }
        return 0.0;
    }

    /**
     * Train the SVM using simplified SMO algorithm.
     */
    public function train($X, $y)
    {
        $numSamples = count($X);
        if ($numSamples === 0) return;

        // Map labels from 0/1 to -1/1
        $yMapped = [];
        for ($i = 0; $i < $numSamples; $i++) {
            $yMapped[$i] = $y[$i] == 1 ? 1.0 : -1.0;
        }

        // Initialize alphas and bias
        $alpha = array_fill(0, $numSamples, 0.0);
        $this->b = 0.0;
        $passes = 0;

        // Precompute kernel matrix for speed
        $kernelMatrix = [];
        for ($i = 0; $i < $numSamples; $i++) {
            $kernelMatrix[$i] = [];
            for ($j = 0; $j < $numSamples; $j++) {
                if ($i <= $j) {
                    $kernelMatrix[$i][$j] = $this->computeKernel($X[$i], $X[$j]);
                } else {
                    $kernelMatrix[$i][$j] = $kernelMatrix[$j][$i];
                }
            }
        }

        // SMO Loop
        while ($passes < $this->maxPasses) {
            $numChangedAlphas = 0;

            for ($i = 0; $i < $numSamples; $i++) {
                // Calculate E_i (prediction error of sample i)
                $f_i = 0.0;
                for ($k = 0; $k < $numSamples; $k++) {
                    $f_i += $alpha[$k] * $yMapped[$k] * $kernelMatrix[$k][$i];
                }
                $f_i += $this->b;
                $E_i = $f_i - $yMapped[$i];

                // Check KKT conditions
                if (($yMapped[$i] * $E_i < -$this->tol && $alpha[$i] < $this->C) || 
                    ($yMapped[$i] * $E_i > $this->tol && $alpha[$i] > 0.0)) {
                    
                    // Select j randomly (j != i)
                    $j = $i;
                    while ($j === $i) {
                        $j = rand(0, $numSamples - 1);
                    }

                    // Calculate E_j
                    $f_j = 0.0;
                    for ($k = 0; $k < $numSamples; $k++) {
                        $f_j += $alpha[$k] * $yMapped[$k] * $kernelMatrix[$k][$j];
                    }
                    $f_j += $this->b;
                    $E_j = $f_j - $yMapped[$j];

                    // Save old alphas
                    $alphaOld_i = $alpha[$i];
                    $alphaOld_j = $alpha[$j];

                    // Calculate bounds L and H
                    if ($yMapped[$i] !== $yMapped[$j]) {
                        $L = max(0.0, $alpha[$j] - $alpha[$i]);
                        $H = min($this->C, $this->C + $alpha[$j] - $alpha[$i]);
                    } else {
                        $L = max(0.0, $alpha[$i] + $alpha[$j] - $this->C);
                        $H = min($this->C, $alpha[$i] + $alpha[$j]);
                    }

                    if (abs($L - $H) < 1e-5) continue;

                    // Calculate eta
                    $eta = 2.0 * $kernelMatrix[$i][$j] - $kernelMatrix[$i][$i] - $kernelMatrix[$j][$j];
                    if ($eta >= 0.0) continue;

                    // Update alpha_j
                    $alphaNew_j = $alpha[$j] - ($yMapped[$j] * ($E_i - $E_j)) / $eta;

                    // Clip alpha_j
                    if ($alphaNew_j > $H) {
                        $alphaNew_j = $H;
                    } elseif ($alphaNew_j < $L) {
                        $alphaNew_j = $L;
                    }

                    if (abs($alphaNew_j - $alphaOld_j) < 1e-5) continue;

                    // Update alpha_i based on alpha_j change
                    $alpha[$i] += $yMapped[$i] * $yMapped[$j] * ($alphaOld_j - $alphaNew_j);
                    $alpha[$j] = $alphaNew_j;

                    // Calculate b1 and b2
                    $b1 = $this->b - $E_i 
                        - $yMapped[$i] * ($alpha[$i] - $alphaOld_i) * $kernelMatrix[$i][$i] 
                        - $yMapped[$j] * ($alpha[$j] - $alphaOld_j) * $kernelMatrix[$i][$j];
                        
                    $b2 = $this->b - $E_j 
                        - $yMapped[$i] * ($alpha[$i] - $alphaOld_i) * $kernelMatrix[$i][$j] 
                        - $yMapped[$j] * ($alpha[$j] - $alphaOld_j) * $kernelMatrix[$j][$j];

                    // Determine bias
                    if ($alpha[$i] > 0.0 && $alpha[$i] < $this->C) {
                        $this->b = $b1;
                    } elseif ($alpha[$j] > 0.0 && $alpha[$j] < $this->C) {
                        $this->b = $b2;
                    } else {
                        $this->b = ($b1 + $b2) / 2.0;
                    }

                    $numChangedAlphas++;
                }
            }

            if ($numChangedAlphas === 0) {
                $passes++;
            } else {
                $passes = 0;
            }
        }

        // Store Support Vectors only to compress model and speed up prediction
        $this->supportVectors = [];
        $this->supportAlphas = [];
        $this->supportLabels = [];

        for ($i = 0; $i < $numSamples; $i++) {
            if ($alpha[$i] > 1e-5) {
                $this->supportVectors[] = $X[$i];
                $this->supportAlphas[] = $alpha[$i];
                $this->supportLabels[] = $yMapped[$i];
            }
        }
    }

    /**
     * Compute SVM decision function score.
     */
    public function decisionFunction($x)
    {
        $score = 0.0;
        $numSVs = count($this->supportVectors);
        
        for ($i = 0; $i < $numSVs; $i++) {
            $score += $this->supportAlphas[$i] * $this->supportLabels[$i] * $this->computeKernel($this->supportVectors[$i], $x);
        }
        $score += $this->b;
        return $score;
    }

    /**
     * Predict class for a single sample.
     * Returns 1 (Ekstrem) or 0 (Normal).
     */
    public function predictSample($x)
    {
        $score = $this->decisionFunction($x);
        return $score >= 0.0 ? 1 : 0;
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
}

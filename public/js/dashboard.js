document.addEventListener('DOMContentLoaded', function () {
    // Select training elements
    const trainForm = document.getElementById('train-form');
    const spinner = document.getElementById('loading-spinner');
    const resultsContainer = document.getElementById('results-container');
    const errorAlert = document.getElementById('js-error-alert');
    const errorText = document.getElementById('js-error-text');

    // Chart.js instances
    let rocChart = null;

    if (trainForm) {
        trainForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Hide results & errors, show loading spinner
            resultsContainer.style.display = 'none';
            errorAlert.style.display = 'none';
            spinner.style.display = 'flex';

            // Get form data
            const formData = new FormData(trainForm);

            // Fetch training
            fetch(trainForm.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                spinner.style.display = 'none';
                if (data.success) {
                    resultsContainer.style.display = 'block';
                    
                    // 1. Update Metrics
                    updateMetricsTable(data.metrics);

                    // 2. Update Confusion Matrices
                    updateConfusionMatrix('rf', data.metrics.rf.confusion_matrix);
                    updateConfusionMatrix('svm', data.metrics.svm.confusion_matrix);

                    // 3. Render ROC Curve Chart
                    renderRocChart(data.roc.rf, data.roc.svm);

                    // Scroll to results
                    resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    showError(data.message || 'Terjadi kesalahan saat melatih model.');
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                showError(error.message || 'Gagal menghubungi server. Pastikan server lokal Anda aktif.');
            });
        });
    }

    function updateMetricsTable(metrics) {
        // Random Forest
        document.getElementById('rf-accuracy').innerText = metrics.rf.accuracy + '%';
        document.getElementById('rf-precision').innerText = metrics.rf.precision + '%';
        document.getElementById('rf-recall').innerText = metrics.rf.recall + '%';
        document.getElementById('rf-f1').innerText = metrics.rf.f1_score + '%';
        document.getElementById('rf-time').innerText = metrics.rf.time + ' ms';

        // SVM
        document.getElementById('svm-accuracy').innerText = metrics.svm.accuracy + '%';
        document.getElementById('svm-precision').innerText = metrics.svm.precision + '%';
        document.getElementById('svm-recall').innerText = metrics.svm.recall + '%';
        document.getElementById('svm-f1').innerText = metrics.svm.f1_score + '%';
        document.getElementById('svm-time').innerText = metrics.svm.time + ' ms';
    }

    function updateConfusionMatrix(prefix, cm) {
        document.getElementById(`${prefix}-tn`).innerText = cm.tn;
        document.getElementById(`${prefix}-fp`).innerText = cm.fp;
        document.getElementById(`${prefix}-fn`).innerText = cm.fn;
        document.getElementById(`${prefix}-tp`).innerText = cm.tp;
    }

    function renderRocChart(rfRoc, svmRoc) {
        const ctx = document.getElementById('rocChartCanvas').getContext('2d');

        // Destroy previous chart if exists
        if (rocChart) {
            rocChart.destroy();
        }

        // Format data points for Chart.js scatter/line
        const rfPoints = rfRoc.map(p => ({ x: p.fpr, y: p.tpr }));
        const svmPoints = svmRoc.map(p => ({ x: p.fpr, y: p.tpr }));

        // Diagonal baseline
        const baselinePoints = [
            { x: 0, y: 0 },
            { x: 1, y: 1 }
        ];

        rocChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Random Forest',
                        data: rfPoints,
                        borderColor: '#10b981', // Emerald
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.1,
                        fill: false
                    },
                    {
                        label: 'Support Vector Machine (SVM)',
                        data: svmPoints,
                        borderColor: '#06b6d4', // Cyan
                        backgroundColor: 'rgba(6, 182, 212, 0.05)',
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.1,
                        fill: false
                    },
                    {
                        label: 'Random Guess',
                        data: baselinePoints,
                        borderColor: 'rgba(148, 163, 184, 0.4)', // Slate gray
                        borderWidth: 1,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'False Positive Rate (FPR)',
                            color: '#94a3b8',
                            font: {
                                family: 'Outfit',
                                size: 12
                            }
                        },
                        min: 0,
                        max: 1,
                        ticks: {
                            color: '#64748b'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        }
                    },
                    y: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: 'True Positive Rate (TPR) / Recall',
                            color: '#94a3b8',
                            font: {
                                family: 'Outfit',
                                size: 12
                            }
                        },
                        min: 0,
                        max: 1,
                        ticks: {
                            color: '#64748b'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#f8fafc',
                            font: {
                                family: 'Outfit',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: (FPR: ${context.parsed.x.toFixed(3)}, TPR: ${context.parsed.y.toFixed(3)})`;
                            }
                        }
                    }
                }
            }
        });
    }

    function showError(message) {
        errorText.innerText = message;
        errorAlert.style.display = 'flex';
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

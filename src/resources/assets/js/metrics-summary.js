let theme = getActiveThemeColor();
const metricDefaultColor = 'rgba(210, 214, 222, 1)';
const metricThemeColors = colors[theme];
let renderedCharts = {};
let globalChartOptions = {
    maintainAspectRatio: false,
    tooltips: {
        mode: 'index',
        intersect: false,
        caretPadding: '8px',
        callbacks: {
            label: (item, data) => {
                let _dataSet = data.datasets[item.datasetIndex];
                return _dataSet.label.toString() + ': ' + numberToAbbreviation(_dataSet.data[item.index]).toString();
            }
        }
    },
    elements: {
        point:{
            radius: 0
        }
    },
    legend: {
        display: false,
    },
};

$(window).ready(() => {
    summaryAll();
    summarySpecificUser();
    topShips();
    topPilots();
});

function summaryAll() {

    $.get(summaryAllDataUrl, (summaryAllData) => {
            let MonthlySummaryChartEl = document.getElementById('summaryAllChart').getContext('2d');
            let MonthlySummaryChart = new Chart(MonthlySummaryChartEl, {
                type: 'line',
                data: {
                    labels: summaryAllData.dt,
                    datasets: [
                        {
                            label: 'Requests',
                            data: summaryAllData.requests,
                            backgroundColor: convertHex(metricThemeColors[1],70),
                            borderColor: convertHex(metricThemeColors[1],70),
                            yAxisID: 'yAxes2',
                            pointHitRadius: 20,
                        },{
                            label: 'Payouts',
                            data: summaryAllData.payouts,
                            backgroundColor: metricDefaultColor,
                            borderColor: metricDefaultColor,
                            yAxisID: 'yAxes1',
                            pointHitRadius: 20,
                        },
                    ],
                },
                options: {
                    ...globalChartOptions,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'month',
                            },
                            ticks: {
                                source: 'data'
                            },
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        }],
                        yAxes: [
                            {
                                type: 'linear',
                                id: 'yAxes1',
                                display: true,
                                position: 'left',
                                gridLines: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    callback: numberToAbbreviation
                                },
                            },{
                                type: 'linear',
                                id: 'yAxes2',
                                display: true,
                                position: 'right',
                                gridLines: {
                                    drawOnChartArea: false,
                                }
                            },
                        ],
                    },
                }
            });
            renderedCharts.MonthlySummaryChart = MonthlySummaryChart;
        })
        .fail(err => {
            console.log('summaryAllChart failed')
            console.log(err);
        });
}

function summarySpecificUser() {
    let specificUserEL = document.getElementById('specificUserSelected');
    let user_group_id = specificUserEL.options[specificUserEL.selectedIndex].value;
    
    ['summarySpecificUserSummaryChart', 'summarySpecificUserShipChart'].forEach( ctx => {
        if(ctx in renderedCharts){
            renderedCharts[ctx].destroy();
        }
    });

    $.get([summaryUserDataUrl, user_group_id, '15'].join('/'), (summarySpecificUserData) => {
            let summarySpecificUserSummaryEl = document.getElementById('specificUserSummaryChart').getContext('2d');
            let summarySpecificUserSummaryChart = new Chart(summarySpecificUserSummaryEl, {
                type: 'line',
                data: {
                    labels: summarySpecificUserData.summary.dt,
                    datasets: [
                        {
                            label: 'Requests',
                            data: summarySpecificUserData.summary.requests,
                            backgroundColor: convertHex(metricThemeColors[1],70),
                            borderColor: convertHex(metricThemeColors[1],70),
                            yAxisID: 'yAxes2',
                            pointHitRadius: 20,
                        },{
                            label: 'Payouts',
                            data: summarySpecificUserData.summary.payouts,
                            backgroundColor: metricDefaultColor,
                            borderColor: metricDefaultColor,
                            yAxisID: 'yAxes1',
                            pointHitRadius: 20,
                        },
                    ],
                },
                options: {
                    ...globalChartOptions,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'month',
                            },
                            ticks: {
                                source: 'data'
                            },
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        }],
                        yAxes: [
                            {
                                type: 'linear',
                                id: 'yAxes1',
                                display: true,
                                position: 'left',
                                gridLines: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    callback: numberToAbbreviation
                                },
                            },{
                                type: 'linear',
                                id: 'yAxes2',
                                display: true,
                                position: 'right',
                                gridLines: {
                                    drawOnChartArea: false,
                                }
                            },
                        ],
                    },
                }
            });
            renderedCharts.summarySpecificUserSummaryChart = summarySpecificUserSummaryChart;

            let summarySpecificUserShipEl = document.getElementById('specificUserShipsChart').getContext('2d');
            let summarySpecificUserShipChart = new Chart(summarySpecificUserShipEl, {
                type: 'bar',
                data: {
                    labels: summarySpecificUserData.ships.ship,
                    datasets: [
                        {
                            label: 'Requests',
                            data: summarySpecificUserData.ships.requests,
                            backgroundColor: convertHex(metricThemeColors[1],70),
                            borderColor: convertHex(metricThemeColors[1],70),
                            yAxisID: 'yAxes2',
                            pointHitRadius: 20,
                        },{
                            label: 'Payouts',
                            data: summarySpecificUserData.ships.payouts,
                            backgroundColor: metricDefaultColor,
                            borderColor: metricDefaultColor,
                            yAxisID: 'yAxes1',
                            pointHitRadius: 20,
                        },
                    ],
                },
                options: {
                    ...globalChartOptions,
                    scales: {
                        yAxes: [
                            {
                                type: 'linear',
                                id: 'yAxes1',
                                display: true,
                                position: 'left',
                                gridLines: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    callback: numberToAbbreviation
                                },
                            },{
                                type: 'linear',
                                id: 'yAxes2',
                                display: true,
                                position: 'right',
                                gridLines: {
                                    drawOnChartArea: false,
                                }
                            },
                        ],
                    },
                }
            });
            renderedCharts.summarySpecificUserShipChart = summarySpecificUserShipChart;
        })
        .fail(err => {
            console.log('summarySpecificUserChart failed')
            console.log(err);
        });
}

function topShips() {
    $.get(topShipsDataUrl, (topShipsData) => {
            let topShipsChartEl = document.getElementById('topShipsChart').getContext('2d');
            let topShipsChart = new Chart(topShipsChartEl, {
                type: 'bar',
                data: {
                    labels: topShipsData.ships,
                    datasets: [{
                        label: 'Requests',
                        data: topShipsData.requests,
                        borderColor: convertHex(metricThemeColors[1],70),
                        backgroundColor: convertHex(metricThemeColors[1],70),
                        yAxisID: 'yAxes2',
                    },{
                        label: 'Payouts',
                        data: topShipsData.payouts,
                        borderColor: metricDefaultColor,
                        backgroundColor: metricDefaultColor,
                        yAxisID: 'yAxes1',
                    },]
                },
                options: {
                    ...globalChartOptions,
                    scales: {
                        yAxes: [{
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'yAxes1',
                            gridLines: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                callback: numberToAbbreviation
                            },
                        }, {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            id: 'yAxes2',
                            gridLines: {
                                drawOnChartArea: false,
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        }]
                    },
                }
            });
        })
        .fail(err => {
            console.log('topShipsChart failed')
            console.log(err);
        });
}

function topPilots() {
    $.get(topPilotsDataUrl, (topPilotsData) => {
            let topPilotsChartEl = document.getElementById('topPilotsChart').getContext('2d');
            let topPilotsChart = new Chart(topPilotsChartEl, {
                type: 'bar',
                data: {
                    labels: topPilotsData.main,
                    datasets: [{
                        label: 'Requests',
                        data: topPilotsData.requests,
                        borderColor: convertHex(metricThemeColors[1],70),
                        backgroundColor: convertHex(metricThemeColors[1],70),
                        yAxisID: 'yAxes2',
                    },{
                        label: 'Payouts',
                        data: topPilotsData.payouts,
                        borderColor: metricDefaultColor,
                        backgroundColor: metricDefaultColor,
                        yAxisID: 'yAxes1',
                    },]
                },
                options: {
                    ...globalChartOptions,
                    scales: {
                        yAxes: [{
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'yAxes1',
                            gridLines: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                callback: numberToAbbreviation
                            },
                        }, {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            id: 'yAxes2',
                            gridLines: {
                                drawOnChartArea: false,
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        }]
                    },
                }
            });
        })
        .fail(err => {
            console.log('topPilotsChart failed')
            console.log(err);
        });
}

function numberToCurrency(value, index, values) {
    value = value.toString();
    value = value.split(/(?=(?:...)*$)/);
    value = value.join('.');
    return '$' + value;
}

function numberToAbbreviation(num, digits=2) {
    var si = [
        { value: 1, symbol: "" },
        { value: 1E3, symbol: "k" },
        { value: 1E6, symbol: "M" },
        { value: 1E9, symbol: "B" },
        { value: 1E12, symbol: "t" },
        { value: 1E15, symbol: "q" },
        { value: 1E18, symbol: "Q" }
    ];
    var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    var i;
    for (i = si.length - 1; i > 0; i--) {
        if (num >= si[i].value) {
            break;
        }
    }
    return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
}

function getActiveThemeColor() {
    var bodyClass = new RegExp(/skin-([a-z0-9_]+)(-light)?/, 'gi').exec($('body').attr('class'));
    if (bodyClass.length > 0)
        return bodyClass[1];
    return '';
}

function convertHex(hex, opacity) {
    hex = hex.replace('#', '');
    r = parseInt(hex.substring(0, 2), 16);
    g = parseInt(hex.substring(2, 4), 16);
    b = parseInt(hex.substring(4, 6), 16);

    result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
    return result;
}
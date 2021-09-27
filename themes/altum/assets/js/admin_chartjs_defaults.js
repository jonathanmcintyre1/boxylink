/* Default chart settings */
Chart.defaults.global.elements.line.borderWidth = 4;
Chart.defaults.global.elements.point.radius = 3;
Chart.defaults.global.elements.point.borderWidth = 6;
Chart.defaults.global.elements.point.hoverBorderWidth = 4;
Chart.defaults.global.elements.point.hoverRadius = 8;

let chart_css = window.getComputedStyle(document.body);

/* Default chart options */
let chart_options = {
    animation: {
        duration: 0
    },
    hover: {
        animationDuration: 0
    },
    responsiveAnimationDuration: 0,
    elements: {
        line: {
            tension: 0
        }
    },
    tooltips: {
        mode: 'index',
        intersect: false,
        xPadding: 12,
        yPadding: 12,
        titleFontColor: chart_css.getPropertyValue('--white'),
        titleSpacing: 30,
        titleFontSize: 16,
        titleFontStyle: 'bold',
        titleMarginBottom: 10,
        bodyFontColor: chart_css.getPropertyValue('--white'),
        bodyFontSize: 14,
        bodySpacing: 10,
        backgroundColor: chart_css.getPropertyValue('--gray-900'),
        footerMarginTop: 10,
        footerFontStyle: 'normal',
        footerFontSize: 12,
        cornerRadius: 4,
        caretSize: 6,
    },
    title: {
        text: '',
        display: true
    },
    scales: {
        yAxes: [{
            gridLines: {
                display: false
            },
            ticks: {
                beginAtZero: true,
                userCallback: (value, index, values) => {
                    if (Math.floor(value) === value) {
                        return nr(value);
                    }
                },
            }
        }],
        xAxes: [{
            gridLines: {
                display: false
            },
            ticks: {
                callback: (tick, index, array) => {
                    return index % 2 ? '' : tick;
                }
            }
        }]
    },
    responsive: true,
    maintainAspectRatio: false
};

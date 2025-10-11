// Dashboard Charts - Versión simple para carga inmediata
console.log('📊 Dashboard Charts Simple cargado');

// Configuración de gráficos
const ChartConfig = {
    opportunities: null, 
    clients: null, 
    sellers: null, 
    leaders: null, 
    performance: null 
};

// Colores consistentes
const Colors = {
    primary: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#06B6D4'],
    primaryDark: ['#1E40AF', '#047857', '#D97706', '#DC2626', '#7C3AED', '#EA580C', '#0891B2']
};

// Configuración común
const CommonChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: { size: 12, weight: '500' }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#374151',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
            intersect: false,
            mode: 'index'
        }
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { 
                font: { size: 11, weight: '500' },
                color: '#6B7280'
            }
        },
        y: {
            grid: { 
                color: '#F3F4F6',
                drawBorder: false
            },
            ticks: { 
                font: { size: 11, weight: '500' },
                color: '#6B7280',
                callback: function(value) {
                    return value.toLocaleString();
                }
            }
        }
    },
    animation: {
        duration: 750,
        easing: 'easeInOutQuart'
    }
};

// Función para crear dataset de barras
function createBarDataset(label, data, colorIndex = 0) {
    return {
        label: label,
        data: data,
        backgroundColor: Colors.primary[colorIndex % Colors.primary.length],
        borderColor: Colors.primaryDark[colorIndex % Colors.primaryDark.length],
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
        hoverBackgroundColor: Colors.primaryDark[colorIndex % Colors.primaryDark.length],
        hoverBorderColor: Colors.primary[colorIndex % Colors.primary.length],
        hoverBorderWidth: 3
    };
}

// Función para crear dataset de líneas
function createLineDataset(label, data, colorIndex = 0) {
    return {
        label: label,
        data: data,
        borderColor: Colors.primary[colorIndex % Colors.primary.length],
        backgroundColor: Colors.primary[colorIndex % Colors.primary.length] + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: Colors.primary[colorIndex % Colors.primary.length],
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8,
        pointHoverBackgroundColor: Colors.primary[colorIndex % Colors.primary.length],
        pointHoverBorderColor: '#ffffff',
        pointHoverBorderWidth: 3
    };
}

// Función simple para inicializar gráficos
function initChartsSimple(chartData) {
    console.log('🚀 Inicializando gráficos (versión simple)...');
    
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está disponible');
        return;
    }
    
    try {
        // Gráfico de Oportunidades
        const oppCtx = document.getElementById('opportunitiesChart');
        if (oppCtx && chartData.opportunitiesByStage) {
            ChartConfig.opportunities = new Chart(oppCtx, {
                type: 'bar',
                data: {
                    labels: chartData.opportunitiesByStage.map(item => item.stage || 'Sin etapa'),
                    datasets: [createBarDataset('Oportunidades', chartData.opportunitiesByStage.map(item => item.count || 0), 0)]
                },
                options: {
                    ...CommonChartOptions,
                    plugins: {
                        ...CommonChartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Oportunidades por Etapa',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de oportunidades inicializado');
        }
        
        // Gráfico de Clientes
        const cliCtx = document.getElementById('clientsChart');
        if (cliCtx && chartData.clientsByStatus) {
            ChartConfig.clients = new Chart(cliCtx, {
                type: 'bar',
                data: {
                    labels: chartData.clientsByStatus.map(item => item.status || 'Sin estado'),
                    datasets: [createBarDataset('Clientes', chartData.clientsByStatus.map(item => item.count || 0), 1)]
                },
                options: {
                    ...CommonChartOptions,
                    plugins: {
                        ...CommonChartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Distribución de Clientes',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de clientes inicializado');
        }
        
        // Gráfico de Vendedores
        const selCtx = document.getElementById('sellersChart');
        if (selCtx && chartData.closedOpportunitiesBySeller) {
            ChartConfig.sellers = new Chart(selCtx, {
                type: 'bar',
                data: {
                    labels: chartData.closedOpportunitiesBySeller.map(item => item.name || 'Sin nombre'),
                    datasets: [
                        {
                            ...createBarDataset('Ventas (S/)', chartData.closedOpportunitiesBySeller.map(item => item.total_sales || 0), 2),
                            yAxisID: 'y'
                        },
                        {
                            ...createBarDataset('Oportunidades', chartData.closedOpportunitiesBySeller.map(item => item.closed_opportunities || 0), 3),
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    ...CommonChartOptions,
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: { callback: value => value.toLocaleString() }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false }
                        }
                    },
                    plugins: {
                        ...CommonChartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Rendimiento de Vendedores',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de vendedores inicializado');
        }
        
        // Gráfico de Líderes
        const leadCtx = document.getElementById('leadersChart');
        if (leadCtx && chartData.leaderPerformance) {
            ChartConfig.leaders = new Chart(leadCtx, {
                type: 'bar',
                data: {
                    labels: chartData.leaderPerformance.map(item => item.name || 'Sin nombre'),
                    datasets: [
                        {
                            ...createBarDataset('Ventas Líder', chartData.leaderPerformance.map(item => item.leader_sales || 0), 4),
                            yAxisID: 'y'
                        },
                        {
                            ...createBarDataset('Ventas Equipo', chartData.leaderPerformance.map(item => item.team_sales || 0), 5),
                            yAxisID: 'y'
                        },
                        {
                            ...createBarDataset('Oportunidades', chartData.leaderPerformance.map(item => item.closed_opportunities || 0), 6),
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    ...CommonChartOptions,
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: { callback: value => 'S/ ' + value.toLocaleString() }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false }
                        }
                    },
                    plugins: {
                        ...CommonChartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Rendimiento de Líderes',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de líderes inicializado');
        }
        
        // Gráfico de Rendimiento
        const perfCtx = document.getElementById('performanceChart');
        if (perfCtx && chartData.advisorPerformance) {
            ChartConfig.performance = new Chart(perfCtx, {
                type: 'line',
                data: {
                    labels: chartData.advisorPerformance.map(item => item.name || 'Sin nombre'),
                    datasets: [
                        createLineDataset('Total Oportunidades', chartData.advisorPerformance.map(item => item.total_opportunities || 0), 0),
                        createLineDataset('Oportunidades Ganadas', chartData.advisorPerformance.map(item => item.won_opportunities || 0), 1)
                    ]
                },
                options: {
                    ...CommonChartOptions,
                    plugins: {
                        ...CommonChartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Rendimiento de Asesores',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de rendimiento inicializado');
        }
        
        console.log('✅ Todos los gráficos inicializados correctamente');
    } catch (error) {
        console.error('❌ Error al inicializar gráficos:', error);
    }
}

// Exportar función para uso global
window.DashboardChartsSimple = {
    initChartsSimple
};

console.log('📊 Dashboard Charts Simple listo para usar');

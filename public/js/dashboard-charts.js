// Dashboard Charts - Versión simplificada para debug
console.log('📊 Dashboard Charts cargado');

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

// Inicializar gráfico de Oportunidades por Etapa
function initOpportunitiesChart(data) {
    try {
        console.log('📈 Inicializando gráfico de oportunidades...');
        const ctx = document.getElementById('opportunitiesChart');
        if (!ctx) {
            console.warn('❌ Canvas opportunitiesChart no encontrado');
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('⚠️  No hay datos para el gráfico de oportunidades');
            return;
        }

        ChartConfig.opportunities = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.stage || 'Sin etapa'),
                datasets: [createBarDataset('Oportunidades', data.map(item => item.count || 0), 0)]
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
    } catch (error) {
        console.error('❌ Error al inicializar gráfico de oportunidades:', error);
    }
}

// Inicializar gráfico de Clientes por Estado
function initClientsChart(data) {
    try {
        console.log('👥 Inicializando gráfico de clientes...');
        const ctx = document.getElementById('clientsChart');
        if (!ctx) {
            console.warn('❌ Canvas clientsChart no encontrado');
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('⚠️  No hay datos para el gráfico de clientes');
            return;
        }

        ChartConfig.clients = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.status || 'Sin estado'),
                datasets: [createBarDataset('Clientes', data.map(item => item.count || 0), 1)]
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
    } catch (error) {
        console.error('❌ Error al inicializar gráfico de clientes:', error);
    }
}

// Inicializar gráfico de Vendedores
function initSellersChart(data) {
    try {
        console.log('💰 Inicializando gráfico de vendedores...');
        const ctx = document.getElementById('sellersChart');
        if (!ctx) {
            console.warn('❌ Canvas sellersChart no encontrado');
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('⚠️  No hay datos para el gráfico de vendedores');
            return;
        }

        ChartConfig.sellers = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.name || 'Sin nombre'),
                datasets: [
                    {
                        ...createBarDataset('Ventas (S/)', data.map(item => item.total_sales || 0), 2),
                        yAxisID: 'y'
                    },
                    {
                        ...createBarDataset('Oportunidades', data.map(item => item.closed_opportunities || 0), 3),
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                ...CommonChartOptions,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11, weight: '500' },
                            color: '#6B7280'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
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
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: { 
                            font: { size: 11, weight: '500' },
                            color: '#6B7280'
                        }
                    }
                },
                plugins: {
                    ...CommonChartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Rendimiento de Vendedores',
                        font: { size: 16, weight: 'bold' }
                    },
                    tooltip: {
                        ...CommonChartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    // Ventas en soles
                                    label += 'S/ ' + context.parsed.y.toLocaleString();
                                } else {
                                    // Oportunidades como número
                                    label += context.parsed.y + ' oportunidades';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico de vendedores inicializado');
    } catch (error) {
        console.error('❌ Error al inicializar gráfico de vendedores:', error);
    }
}

// Inicializar gráfico de Líderes
function initLeadersChart(data) {
    try {
        console.log('👑 Inicializando gráfico de líderes...');
        const ctx = document.getElementById('leadersChart');
        if (!ctx) {
            console.warn('❌ Canvas leadersChart no encontrado');
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('⚠️  No hay datos para el gráfico de líderes');
            return;
        }

        ChartConfig.leaders = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.name || 'Sin nombre'),
                datasets: [
                    {
                        ...createBarDataset('Ventas Líder', data.map(item => item.leader_sales || 0), 4),
                        yAxisID: 'y'
                    },
                    {
                        ...createBarDataset('Ventas Equipo', data.map(item => item.team_sales || 0), 5),
                        yAxisID: 'y'
                    },
                    {
                        ...createBarDataset('Oportunidades', data.map(item => item.closed_opportunities || 0), 6),
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                ...CommonChartOptions,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11, weight: '500' },
                            color: '#6B7280'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { 
                            color: '#F3F4F6',
                            drawBorder: false
                        },
                        ticks: { 
                            font: { size: 11, weight: '500' },
                            color: '#6B7280',
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: { 
                            font: { size: 11, weight: '500' },
                            color: '#6B7280'
                        }
                    }
                },
                plugins: {
                    ...CommonChartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Rendimiento de Líderes',
                        font: { size: 16, weight: 'bold' }
                    },
                    tooltip: {
                        ...CommonChartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0 || context.datasetIndex === 1) {
                                    // Ventas en soles
                                    label += 'S/ ' + context.parsed.y.toLocaleString();
                                } else {
                                    // Oportunidades como número
                                    label += context.parsed.y + ' oportunidades';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico de líderes inicializado');
    } catch (error) {
        console.error('❌ Error al inicializar gráfico de líderes:', error);
    }
}

// Inicializar gráfico de Rendimiento
function initPerformanceChart(data) {
    try {
        console.log('📊 Inicializando gráfico de rendimiento...');
        const ctx = document.getElementById('performanceChart');
        if (!ctx) {
            console.warn('❌ Canvas performanceChart no encontrado');
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('⚠️  No hay datos para el gráfico de rendimiento');
            return;
        }

        ChartConfig.performance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.name || 'Sin nombre'),
                datasets: [
                    createLineDataset('Total Oportunidades', data.map(item => item.total_opportunities || 0), 0),
                    createLineDataset('Oportunidades Ganadas', data.map(item => item.won_opportunities || 0), 1)
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
    } catch (error) {
        console.error('❌ Error al inicializar gráfico de rendimiento:', error);
    }
}

// Inicializar todos los gráficos
function initAllCharts(chartData) {
    console.log('🚀 Inicializando todos los gráficos...');
    console.log('Datos recibidos:', chartData);
    
    try {
        // Verificar que Chart.js esté disponible
        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js no está disponible');
            return;
        }
        
        // Destruir gráficos existentes
        Object.values(ChartConfig).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        
        // Reinicializar configuración
        Object.keys(ChartConfig).forEach(key => {
            ChartConfig[key] = null;
        });
        
        // Inicializar cada gráfico con delay para evitar conflictos
        console.log('📊 Inicializando gráfico de oportunidades...');
        setTimeout(() => initOpportunitiesChart(chartData.opportunitiesByStage || []), 100);
        
        console.log('👥 Inicializando gráfico de clientes...');
        setTimeout(() => initClientsChart(chartData.clientsByStatus || []), 200);
        
        console.log('💰 Inicializando gráfico de vendedores...');
        setTimeout(() => initSellersChart(chartData.closedOpportunitiesBySeller || []), 300);
        
        console.log('👑 Inicializando gráfico de líderes...');
        setTimeout(() => initLeadersChart(chartData.leaderPerformance || []), 400);
        
        console.log('📈 Inicializando gráfico de rendimiento...');
        setTimeout(() => initPerformanceChart(chartData.advisorPerformance || []), 500);
        
        console.log('✅ Proceso de inicialización de gráficos completado');
    } catch (error) {
        console.error('❌ Error al inicializar gráficos:', error);
    }
}

// Función para actualizar gráficos
function updateCharts(data) {
    console.log('🔄 Actualizando gráficos...');
    console.log('Datos de actualización:', data);
    
    try {
        // Actualizar gráfico de oportunidades
        if (ChartConfig.opportunities && data.opportunitiesByStage && Array.isArray(data.opportunitiesByStage)) {
            ChartConfig.opportunities.data.labels = data.opportunitiesByStage.map(i => i.stage || 'Sin etapa');
            ChartConfig.opportunities.data.datasets[0].data = data.opportunitiesByStage.map(i => i.count || 0);
            ChartConfig.opportunities.update('active');
        }
        
        // Actualizar gráfico de clientes
        if (ChartConfig.clients && data.clientsByStatus && Array.isArray(data.clientsByStatus)) {
            ChartConfig.clients.data.labels = data.clientsByStatus.map(i => i.status || 'Sin estado');
            ChartConfig.clients.data.datasets[0].data = data.clientsByStatus.map(i => i.count || 0);
            ChartConfig.clients.update('active');
        }
        
        // Actualizar gráfico de vendedores
        if (ChartConfig.sellers && data.closedOpportunitiesBySeller && Array.isArray(data.closedOpportunitiesBySeller)) {
            const sellers = data.closedOpportunitiesBySeller;
            ChartConfig.sellers.data.labels = sellers.map(i => i.name || 'Sin nombre');
            ChartConfig.sellers.data.datasets[0].data = sellers.map(i => i.total_sales || 0);
            ChartConfig.sellers.data.datasets[1].data = sellers.map(i => i.closed_opportunities || 0);
            ChartConfig.sellers.update('active');
        }
        
        // Actualizar gráfico de líderes
        if (ChartConfig.leaders && data.leaderPerformance && Array.isArray(data.leaderPerformance)) {
            const leaders = data.leaderPerformance;
            ChartConfig.leaders.data.labels = leaders.map(i => i.name || 'Sin nombre');
            ChartConfig.leaders.data.datasets[0].data = leaders.map(i => i.leader_sales || 0);
            ChartConfig.leaders.data.datasets[1].data = leaders.map(i => i.team_sales || 0);
            ChartConfig.leaders.data.datasets[2].data = leaders.map(i => i.closed_opportunities || 0);
            ChartConfig.leaders.update('active');
        }
        
        // Actualizar gráfico de rendimiento
        if (ChartConfig.performance && data.advisorPerformance && Array.isArray(data.advisorPerformance)) {
            const perf = data.advisorPerformance;
            ChartConfig.performance.data.labels = perf.map(i => i.name || 'Sin nombre');
            ChartConfig.performance.data.datasets[0].data = perf.map(i => i.total_opportunities || 0);
            ChartConfig.performance.data.datasets[1].data = perf.map(i => i.won_opportunities || 0);
            ChartConfig.performance.update('active');
        }
        
        console.log('✅ Gráficos actualizados correctamente');
    } catch (error) {
        console.error('❌ Error al actualizar gráficos:', error);
    }
}

// Exportar funciones para uso global
window.DashboardCharts = {
    initAllCharts,
    updateCharts,
    initOpportunitiesChart,
    initClientsChart,
    initSellersChart,
    initLeadersChart,
    initPerformanceChart
};

console.log('📊 Dashboard Charts listo para usar');

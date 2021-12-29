<script>
    Vue.component("bar-chart", {
        extends: VueChartJs.Bar,
        props:{
            options:{type:Object,default:() => ({responsive: true, maintainAspectRatio: false,scales: {yAxes: [{ticks: {min:0,max: 100}}]}})},
            data:{type:Object,required: true},
        },
        mounted () {this.renderChart(this.data, this.options)}
    });
</script>
<script>
    Vue.component("pie-chart", {
        extends: VueChartJs.Pie,
        props:{
            options:{type:Object,default:() => {}},
            data:{type:Object,required: true},
        },
        mounted () {this.renderChart(this.data, this.options)}
    });
</script>

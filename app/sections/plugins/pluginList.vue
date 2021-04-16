<template>
    <div>

        <plugin-modal
            v-if="pluginModal"
            @close-modal="closePluginModal($event)"
            :item="select"
        ></plugin-modal>

        <plugins-table
            ref="plugins"
            :item="{hide_link:false, hide_new_row:true}"
            @event="openPluginModal($event)"
        ></plugins-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            pluginModal: false,
            select:null,
        }
    },
    methods: {
        openPluginModal:function(id){
            this.select=id
            this.pluginModal=true
        },
        closePluginModal:function(event){
            this.pluginModal=false
            this.select=null
            if(event) this.$refs.plugins.getPlugins()
        },
    },
    created:function() {
    },
    components:{
        'plugin-modal': httpVueLoader('./pluginModal.vue' + '?v=' + new Date().getTime()),
        'plugins-table': httpVueLoader('./pluginsTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>

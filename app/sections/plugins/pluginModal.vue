<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
            >
                <v-toolbar-title>
                    {{modalTitle}}
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn
                    icon
                    @click="closeModal(false)"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
                <v-flex xs3 pr-2 pb-3>
                    <v-text-field
                        outlined
                        dense
                        hide-details
                        readonly
                        label="Plugin id"
                        v-model="form.id"
                    ></v-text-field>
                </v-flex>
                <v-flex xs3 pl-2 pr-2 pb-3>
                    <!-- <v-text-field
                        outlined
                        dense
                        hide-details
                        label="Name"
                        v-model="form.name"
                    ></v-text-field> -->
                    <v-select
                        outlined
                        dense
                        hide-details
                        label="Name"
                        :items="name_items"
                        v-model="form.name"
                    ></v-select>
                </v-flex>
                <v-flex xs3 pl-2 pr-2 pb-3>
                    <v-select
                        outlined
                        dense
                        hide-details
                        label="Protocols"
                        multiple
                        :items="protocols_items"
                        v-model="form.protocols"
                    ></v-select>
                </v-flex>
                <v-flex xs3 pb-3>
                    <v-select
                        outlined
                        dense
                        hide-details
                        label="Enabled"
                        :items="[true,false]"
                        v-model="form.enabled"
                    ></v-select>
                </v-flex>
            </v-layout>

            <v-card class="ma-2" v-if="item.id!=null">
                <v-card-title class="pl-2 pr-0 pb-0 pt-0">
                    Link
                    <v-spacer></v-spacer>

                <v-select
                    dense
                    hide-details
                    :items="type_items"
                    v-model="type"
                ></v-select>
                </v-card-title>

                <!-- Components -->
                <services-table
                    v-if="type=='services'"
                    :item="item"
                    @event="closeModal(false,'editService',$event)"
                ></services-table>

                <routes-table
                    v-if="type=='routes'"
                    :item="item"
                    @event="closeModal(false,'editRoute',$event)"
                ></routes-table>

            </v-card>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    @click="submit()"
                >
                    Salva
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            routes:[],
            form:{
                id:null,
                name:null,
                protocols:null,
                enabled:null,
                host:null,
                port:null,
                path:null,
            },
            type:"services",
            type_items:[
                "services",
                "routes"
            ],
            name_items:[
                "oauth2"
            ],
            protocols_items:[
                "grpc",
                "grpcs",
                "http",
                "https"
            ],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:"btn"
                    }
                },
                {
                    text: 'Route ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Hosts',
                    value: 'hosts',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Paths',
                    value: 'paths',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Methods',
                    value: 'methods',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item.id!=null){
                return 'Modify plugin: '+this.item.id
            }else{
                return "Create plugin"
            }

        },
    },
    methods: {
        closeModal:function(value,event,id){
            var tmp={
                event:event,
                refresh:value,
                data:{
                    parent_id:this.form.id,
                    id:id!=undefined ? id : null
                }
            }
            this.$emit('close-modal',tmp)
        },
        submit:function(){
            var self=this
            // this.form.port = parseInt(this.form.port)
            Utils.apiCall("post", "/kong/services",this.form)
            .then(function (response) {
                // PROBLEMA APICALL LA CHIAMATA FALLISCHE MA IL CODICE VIENE ESEGUITO
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.item==null ? 'New service create' : 'Service updated',
                        text: self.item==null ? 'New service create' : 'Service updated',
                    }).then(function(result) {
                        self.closeModal(true)
                    })
                }
            });
        },
        loadData:function(){
            var self=this

            if(this.item.id!=null){
                var params={
                    id:this.item.id
                }
                Utils.apiCall("get", "/kong/plugins",params)
                .then(function (response) {
                    console.log(response)
                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    self.form.protocols=response.data.protocols
                    self.form.enabled=response.data.enabled
                    // self.form.port=response.data.port
                    // self.form.path=response.data.path
                });
            }
        },
        getRoutes:function(){
            var self=this
            Utils.apiCall("get", "/kong/routes")
            .then(function (response) {
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].paths!=null){
                        response.data.data[i].paths=response.data.data[i].paths.join(";")
                    }
                    if(response.data.data[i].methods!=null){
                        response.data.data[i].methods=response.data.data[i].methods.join(";")
                    }
                    if(response.data.data[i].service.id==self.item.id){
                        tmp.push(response.data.data[i])
                    }
                }
                self.routes=tmp
            });
        }
    },
    created:function() {
        console.log(this.item)
        this.loadData()
    },
    components:{
        'services-table': httpVueLoader('./../tables/servicesTable.vue' + '?v=' + new Date().getTime()),
        'routes-table': httpVueLoader('./../tables/routesTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>

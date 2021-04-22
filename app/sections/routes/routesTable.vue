<template>
    <v-data-table
        :headers="headers"
        :items="routes_filtered"
        :items-per-page="5"
        class="elevation-1"
        dense
    >
        <template v-slot:header="{ props }">
            <tr>
                <template v-for="(head,i) in props.headers">
                    <th :key="i">
                        <v-text-field
                            v-if="head.custom.type=='text'"
                            solo
                            dense
                            hide-details
                            clearable
                            @input="filter()"
                            v-model="head.custom.value"
                        ></v-text-field>
                        <v-btn
                            v-if="head.custom.type=='btn'"
                            icon
                            @click="$emit('event',null)"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </th>
                </template>
            </tr>
        </template>
        <template v-slot:item="{ item,index,headers }">
            <tr>
                <template v-for="(header,i) in headers">
                    <td :key="i" v-if="i==0" style="text-align:center">
                        <v-btn
                            icon
                            @click="deleteRow(index)"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn
                            icon
                            @click="sendEvent(index)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                    <td :key="i" v-else>{{ item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function(){
        return{
            routes:[],
            routes_filtered:[],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type: 'btn'
                    },
                },
                {
                    text: 'Route ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
                {
                    text: 'Hosts',
                    value: 'hosts',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
                {
                    text: 'Paths',
                    value: 'paths',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
                {
                    text: 'Methods',
                    value: 'methods',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
                {
                    text: 'Created at',
                    value: 'custom_data',
                    custom:{
                        type:"text",
                        value:null,
                    },
                },
            ],
        }
    },
    props:['item'],
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.routes_filtered[index].id)
        },
        filter:function(){
            this.routes_filtered=JSON.parse(JSON.stringify(this.routes))
            for(var i=this.routes_filtered.length-1;i>=0;i--){
                for(var k=1;k<this.headers.length;k++){
                    if(this.routes_filtered[i][this.headers[k].value]==null) {
                        if(this.headers[k].custom.value!=null&&this.headers[k].custom.value!=''){
                            this.routes_filtered.splice(i,1)
                            break
                        }
                    }else{
                        if(this.headers[k].custom.value==null) continue
                        if(!this.routes_filtered[i][this.headers[k].value].toLowerCase().includes(this.headers[k].custom.value.toLowerCase())){
                            this.routes_filtered.splice(i,1)
                            break
                        }
                    }
                }
            }
        },
        deleteRow:function(index){
            var self=this
            Swal.fire({
                title: 'Delete route',
                text: "Are you sure you want to delete route?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f86c6b',
                cancelButtonColor: '#20a8d8',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Back'
            }).then( function (result) {
                if(result.value){
                    var params={
                        id: self.routes_filtered[index].id
                    }
                    Utils.apiCall("delete", "/kong/routes",params)
                    .then(function (response) {
                        if(response!=undefined){
                            Swal.fire({
                                type: 'success',
                                title: 'Route delete',
                                text: 'Route delete',
                            }).then(function(result) {
                                self.getRoutes()
                            })
                        }
                    });
                }
            })
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
                    if(self.item.id==null){
                        tmp.push(response.data.data[i])
                    }else{
                        if(response.data.data[i].service.id==self.item.id||response.data.data[i].service.id==self.item.parent_id){
                            tmp.push(response.data.data[i])
                        }
                    }
                    if(response.data.data[i].service.id!=null){
                        response.data.data[i].service_id=response.data.data[i].service.id
                    }
                    response.data.data[i].custom_data=moment.unix(response.data.data[i].created_at).format("MM-DD-YYYY HH:mm:ss")
                }
                self.routes=tmp
                self.routes_filtered=self.routes
                self.filter()
            });
        }
    },
    created:function() {
        this.getRoutes();
    },
}
</script>

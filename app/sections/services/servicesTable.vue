<template>
    <v-data-table
        :headers="headers"
        :items="services_filtered"
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
                            @click="sendEvent()"
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
                        <v-btn icon
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
                    <td :key="i" v-else>{{item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function() {
        return {
            services:[],
            services_filtered:[],
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
                    text: 'Service ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Host',
                    value: 'host',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Path',
                    value: 'path',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Protocol',
                    value: 'protocol',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Created at',
                    value: 'custom_data',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
            ],
        }
    },
    props:['service_id'],
    methods: {
        sendEvent:function(index){
            this.$emit('event',index!=undefined ? this.services_filtered[index].id : null)
        },
        filter:function(){
            this.services_filtered=JSON.parse(JSON.stringify(this.services))
            for(var i=this.services_filtered.length-1;i>=0;i--){
                for(var k=1;k<this.headers.length;k++){
                    if(this.services_filtered[i][this.headers[k].value]==null) {
                        if(this.headers[k].custom.value!=null&&this.headers[k].custom.value!=''){
                            this.services_filtered.splice(i,1)
                            break
                        }
                    }else{
                        if(this.headers[k].custom.value==null) continue
                        if(!this.services_filtered[i][this.headers[k].value].toLowerCase().includes(this.headers[k].custom.value.toLowerCase())){
                            this.services_filtered.splice(i,1)
                            break
                        }
                    }
                }
            }
        },
        deleteRow:function(index){
            var self=this
            Swal.fire({
                title: 'Delete service',
                text: "Are you sure you want to delete service?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f86c6b',
                cancelButtonColor: '#20a8d8',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Back'
            }).then( function (result) {
                if(result.value){
                    var params={
                        id: self.services_filtered[index].id
                    }
                    Utils.apiCall("delete", "/kong/services",params)
                    .then(function (response) {
                        if(response!=undefined){
                            Swal.fire({
                                type: 'success',
                                title: 'Service delete',
                                text: 'Service delete',
                            }).then(function(result) {
                                self.getServices()
                            })
                        }
                    });
                }
            })
        },
        getServices:function(){
            var self=this
            Utils.apiCall("get", "/kong/services")
            .then(function (response) {
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom_data=moment.unix(response.data.data[i].created_at).format("MM-DD-YYYY HH:mm:ss")
                }
                self.services=response.data.data
                self.services_filtered=self.services
                self.filter()
            });
        },
    },
    created:function() {
        this.getServices()
    },
}
</script>

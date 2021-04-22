<template>
    <v-data-table
        :headers="headers"
        :items="plugins_filtered"
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
                            v-model="head.custom.value"
                            @input="filter()"
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
                    <td :key="i" v-else>{{item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function(){
        return{
            plugins:[],
            plugins_filtered:[],
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
                    text: 'Plugin ID',
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
                    text: 'Enabled',
                    value: 'enabled',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Protocols',
                    value: 'protocols',
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
    props:['item'],
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.plugins_filtered[index].id)
        },
        filter:function(){
            this.plugins_filtered=JSON.parse(JSON.stringify(this.plugins))
            for(var i=this.plugins_filtered.length-1;i>=0;i--){
                for(var k=1;k<this.headers.length;k++){
                    if(this.plugins_filtered[i][this.headers[k].value]==null) {
                        if(this.headers[k].custom.value!=null&&this.headers[k].custom.value!=''){
                            this.plugins_filtered.splice(i,1)
                            break
                        }
                    }else{
                        if(this.headers[k].custom.value==null) continue
                        if(!this.plugins_filtered[i][this.headers[k].value].toLowerCase().includes(this.headers[k].custom.value.toLowerCase())){
                            this.plugins_filtered.splice(i,1)
                            break
                        }
                    }
                }
            }
        },
        deleteRow:function(index){
            var self=this
            Swal.fire({
                title: 'Delete plugin',
                text: "Are you sure you want to delete plugin?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f86c6b',
                cancelButtonColor: '#20a8d8',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Back'
            }).then( function (result) {
                if(result.value){
                    var params={
                        id: self.plugins_filtered[index].id
                    }
                    Utils.apiCall("delete", "/kong/plugins",params)
                    .then(function (response) {
                        if(response!=undefined){
                            Swal.fire({
                                type: 'success',
                                title: 'Plugin delete',
                                text: 'Plugin delete',
                            }).then(function(result) {
                                self.getPlugins()
                            })
                        }
                    });
                }
            })
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                console.log(response)
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].protocols!=null){
                        response.data.data[i].protocols=response.data.data[i].protocols.join(";")
                    }
                    response.data.data[i].custom_data=moment.unix(response.data.data[i].created_at).format("MM-DD-YYYY HH:mm:ss")
                    tmp.push(response.data.data[i])
                }
                self.plugins=tmp
                self.plugins_filtered=self.plugins
                self.filter()
            });
        }
    },
    created:function() {
        this.getPlugins();
    },
}
</script>

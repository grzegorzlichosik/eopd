import {defineStore} from "pinia";


export const useLocationStore = defineStore("location",{
    state:()=>{
        return {
            is_locationEnable:false,
            location_center:{lat:null,lng:null},
            confirm_address:false,
            address:"",
            is_locationVisible: true,
            is_latLon: true,
            entireLocation:[],
            updated_location: null,
            gmap_address: false,
            default_address: true,
            input_address:true,
        }
    }
})

import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params = {}) {
    console.log("TEST params:  ", params);
    
    // Remove the 'id' property from the params object
   // delete params.idmember;
  
    const ep = apiPrefix + "/v1/entities/tbl-payments/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    console.log("URL: " + url);
    return resource.get(url);
  },
  

async read(idmember) {
  const ep = apiPrefix + "/v1/entities/tbl-payments/read";
  const qs = QueryString({ idmember: idmember });
  const url = ep + qs;
  
  try {

    const response = await resource.get(url);
    console.log("API response:", response); // Log the entire API response

    if (response.data && response.data.data) {
      const filteredData = response.data.data.filter(item => {
        console.log("item.idmember1:", item.idmember); 
        console.log("item.idmember2:", Number(idmember));// Log item.idmember
        return Number(item.idmember) == Number(idmember); // Convert both values to strings before comparing them
      });

      console.log("Filtered data:", filteredData); // Log the filtered data
      return filteredData;
    } else {
      console.error("Unexpected API response structure:", response);
      return [];
    }
  } catch (error) {
    console.error("tetetAPI Error:", error);
    return [];
  }
},

/*
  listByMemberId(idmember) {
    console.log("TEST idmember2 " + idmember);
    const ep = apiPrefix + "/v1/entities/tbl-payments";
    const qs = QueryString({ id: idmember });// This line creates a query string with the idmember
    const url = ep + qs; // This line appends the query string to the endpoint URL
    console.log("TEST idmember3" + url);
    return resource.get(url); 
  },

*/


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-payments";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-payments" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-payments/read?slug=tbl-payments&idmember={idmember}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-payments/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/tbl-payments/edit", data);
  },


update(data = []) {
  const ep = apiPrefix + "/v1/entities/tbl-payments/update";
  return resource.put(ep, data);
},


add(data) {

  if (!data.paydate || data.paydate.trim() === '') {
    data.paydate = null;
  }
  if (!data.opendate || data.opendate.trim() === '') {
    data.opendate = null;
  }
  return resource.post(apiPrefix + "/v1/entities/tbl-payments/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "tbl-payments",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/tbl-payments/delete", { data: paramData });
},


citanjenasiclanovi(params = {}) {

  const ep = apiPrefix + "/v1/entities/tbl-payments/citanjenasiclanovi";
  const qs = QueryString(params);
  const url = ep + qs;
  console.log("URL: " + url);
  return resource.get(url);
},


};

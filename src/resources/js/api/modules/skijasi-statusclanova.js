import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-member-status";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/tbl-member-status/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-member-status/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-member-status/read?slug=tbl-member-status&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  edit(data) {
    if (!data.endstatusdate || data.endstatusdate.trim() === '') {
      delete data.endstatusdate;
  }

    return resource.put(apiPrefix + "/v1/entities/tbl-member-status/edit", data);
  },

  add(data) {

    if (!data.statusdate || data.statusdate.trim() === '') {
      data.statusdate = null;
    }
  

    return resource.post(apiPrefix + "/v1/entities/tbl-member-status/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-member-status",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-member-status/delete", { data: paramData });
  },
  
  
  };
  
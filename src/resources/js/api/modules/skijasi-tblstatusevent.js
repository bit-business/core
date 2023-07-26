import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-status-event";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-status-event" + "/all";
    const url = ep;
    return resource.get(url);
  },


  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/tbl-status-event/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    console.log("URL ISIA: " + url);
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-status-event/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-status-event/read?slug=tbl-status-event&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-status-event/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-status-event/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-status-event",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-status-event/delete", { data: paramData });
  },
  
  
  };
  
import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-licence";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {
    console.log("TEST params licence:  ", params);

    const ep = apiPrefix + "/v1/entities/tbl-licence/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    console.log("URL LICENCE: " + url);
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-licence/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  read2(data) {
    const ep = apiPrefix + "/v1/entities/tbl-licence/read2";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-licence/read?slug=tbl-licence&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-licence/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-licence/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-licence",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-licence/delete", { data: paramData });
  },
  



  };
  
import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-member";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {
    console.log("TEST params ISIA:  ", params);

    const ep = apiPrefix + "/v1/entities/tbl-isia-member/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    console.log("URL ISIA: " + url);
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-member/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  read2(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-member/read2";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-member/read?slug=tbl-isia-member&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  generatePdf(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdf";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-isia-member/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-isia-member/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-isia-member",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-isia-member/delete", { data: paramData });
  },
  



  generateisiagodinu(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-isia-member/generateisiagodinu", data);
  },
  

  };
  
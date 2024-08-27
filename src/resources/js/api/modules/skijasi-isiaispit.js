import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-ispit";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/tbl-isia-ispit/citanje";
    const qs = QueryString(params);
    const url = ep + qs;

    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-ispit/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  read2(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-ispit/read2";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-isia-ispit/read?slug=tbl-isia-ispit&id={id}";
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
    return resource.put(apiPrefix + "/v1/entities/tbl-isia-ispit/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-isia-ispit/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-isia-ispit",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-isia-ispit/delete", { data: paramData });
  },
  
  
  };
  
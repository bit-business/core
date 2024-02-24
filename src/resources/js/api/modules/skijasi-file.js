import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  view(file) {
    const data = {
      file,
    };
    const ep = apiPrefix + "/v1/file/view";
    const qs = QueryString(data);
    const url = ep + qs;
    return url;
  },

  upload(files) {
    return resource.post(apiPrefix + "/v1/file/upload", {
      files: files,
    });
  },

  download(file) {
    const data = {
      file,
    };
    const ep = apiPrefix + "/v1/file/download";
    const qs = QueryString(data);
    const url = ep + qs;
    return url;
  },

  browseUsingLfm(data = {}) {
    const ep = apiPrefix + "/v1/file/browse/lfm";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  uploadUsingLfm(files) {
    return resource.post(apiPrefix + "/v1/file/upload/lfm", files);
  },

  delete(data) {
    const ep = apiPrefix + "/v1/file/delete";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  deleteUsingLfm(data) {
    const ep = apiPrefix + "/v1/file/delete/lfm";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browseConfiguration(data = {}) {
    const ep = apiPrefix + "/v1/file/mimetypes";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  customuploadfile(files) {
    return resource.post(apiPrefix + "/v1/file/upload/custom", files);
  },
  customuploadfilevijesti(files) {
    return resource.post(apiPrefix + "/v1/file/upload/customvijesti", files);
  },

  customuploadfiledokumenti(files) {
    return resource.post(apiPrefix + "/v1/file/upload/customdokumenti", files);
  },

  customuploadfiledokumentictt(files) {
    return resource.post(apiPrefix + "/v1/file/upload/customdokumentictt", files);
  },


};
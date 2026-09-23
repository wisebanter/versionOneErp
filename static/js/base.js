class Api {
  constructor({ apiVersion = "v1", basefile = "" } = {}) {
    this.apiVersion = apiVersion;

    const uri = window.location.origin;
    const pathname = window.location.pathname;

    // Clean and split path segments
    const pathSegments = pathname
      .replace(/\\/g, "/")
      .split("/")
      .filter(Boolean);

    // ✅ Only keep the FIRST segment as the base project directory.
    //    If it looks like a file (contains a dot), treat it as root.
    let baseDir = pathSegments.length > 0 ? pathSegments[0] : "";
    if (baseDir.includes(".")) baseDir = "";

    const basePath = baseDir ? `/${baseDir}` : "";

    // Formulate properties
    this.formulated = {
      baseUrlRoute: `${uri}${pathname}`,
      baseUrlPath: `${uri}${basePath}/`,      // always ends with "/"
      baseDirectory: basePath || "/",
      pathInfo: pathname,
      basefile: basefile.replace(/^\/+|\/+$/g, ""),
    };
  }

  // Generate full API endpoint path
  getEndpoint(endpoint = "") {
    const cleanPath = endpoint.replace(/^\/+/, "");
    const versionPrefix = this.apiVersion ? `/${this.apiVersion}` : "";
    const basefilePrefix = this.formulated.basefile
      ? `/${this.formulated.basefile}`
      : "";

    // Build URL and remove duplicate slashes (except after http:// or https://)
    return `${this.formulated.baseUrlPath}${versionPrefix}${basefilePrefix}/${cleanPath}`
      .replace(/([^:]\/)\/+/g, "$1");
  }

  // Helper method to detect and normalize JSON objects and strings
  getValueType(val) {
    if (typeof val === "object" && val !== null && val.constructor === Object) {
      return { type: "jsonObject", value: val };
    } else if (typeof val === "string") {
      const trimmed = val.trim();

      if (
        (trimmed.startsWith("{") && trimmed.endsWith("}")) ||
        (trimmed.startsWith("[") && trimmed.endsWith("]"))
      ) {
        try {
          const parsed = JSON.parse(val);
          return { type: "jsonObject", value: parsed };
        } catch (e) {
          return { type: "string", value: val };
        }
      } else {
        return { type: "string", value: val };
      }
    } else {
      return { type: typeof val, value: val };
    }
  }

  // Perform AJAX request using jQuery
  ajaxRequest({
    endpoint = "",
    method = "GET",
    dataParam = {},
    debug = false,
    onBefore = null,
    onSuccess = null,
    onError = null,
    onAfter = null,
  } = {}) {
    return $.ajax({
      url: this.getEndpoint(endpoint),
      type: method.toUpperCase(),
      data: dataParam,
      beforeSend: (xhr) => {
        if (typeof onBefore === "function") {
          onBefore(xhr);
        } else if (debug) {
          console.log("Preparing request...", xhr);
        }
      },
      success: (response, status, xhr) => {
        const decoded = this.getValueType(response);

        if (typeof onSuccess === "function") {
          if (decoded.type === "jsonObject") {
            onSuccess(decoded.value, status, xhr);
          } else {
            const fallbackResult = new Results({
              status: false,
              // message: "Response is not valid JSON",
              message: decoded.value,
            });
            onSuccess(fallbackResult, status, xhr);
          }
        }

        if (debug) {
          console.log("Request successful:", decoded.value);
        }
      },
      error: (xhr, status, error) => {
        const decoded = this.getValueType(xhr.responseText);

        if (typeof onError === "function") {
          if (decoded.type === "jsonObject") {
            onError(decoded.value, status, xhr);
          } else {
            const fallbackResult = new Results({
              status: false,
              // message: "Response is not valid JSON",
              message: decoded.value,
            });
            onError(fallbackResult, status, xhr);
          }
        }

        if (debug) {
          console.error("AJAX Error:", error, decoded.value);
        }
      },
      complete: (xhr, status) => {
        if (typeof onAfter === "function") {
          onAfter(xhr, status);
        }
        if (debug) {
          // console.log("Request completed.");
        }
      },
    });
  }
}

class Results {
  constructor({ status = false, message = "", values = [] } = {}) {
    this.status = status;
    this.message = message;
  }

  isSuccess() {
    return this.status === true;
  }
}

/*
// Execution block
$(document).ready(function () {
  const api = new Api({ apiVersion: "v1", basefile: "index.php" });

  console.log(api.getEndpoint("users"));

  // Example AJAX call usage
  api.ajaxRequest({
    endpoint: "users",
    method: "GET",
    dataParam: { name: "John" },
    onSuccess: (data) => {
      console.log("Handled success:", data);
    },
    onError: (errorData) => {
      console.log("Handled error:", errorData);
    },
  });
});
*/

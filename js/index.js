var app = new Vue({
  el: '#app',
  data() {
    return {
      activePanels: [], // 当前打开的面板
      headers: '', //附加头部信息
      doc: [], //文档内容
      key: '', //生成签名的key
      submitting: false, // 提交状态
    }
  },

  created() {
    this.getApiDoc();
  },

  methods: {
    // 获取接口文档数据
    getApiDoc() {
      $.ajax({
        url: './doc.php',
        type: 'GET',
        dataType: 'json',
        success: res => {
          if (res.errno == 1) {
            this.doc = res.data;
            this.key = res.key;
          }
        },
        error: err => {
          this.$message.error(err.statusText);
        },
      })
    },

    // 提交表单
    submit(ev, item) {
      this.submitting = true;

      var form = $(ev.target).serializeJSON();
      form = JSON.stringify(this.filter(form));
      var sign = this.sign(form);

      $(ev.target).find('.panel-result').html('');
      
      $.ajax({
        url: item.url + '?sign=' + sign,
        type: item.method.length > 3 ? 'POST' : 'GET',
        data: form,
        dataType: 'json',
        contentType: 'application/json',
        complete: (xhr, status) => {
          this.submitting = false;
          var res;
          if (status == 'success') {
            res = xhr.responseJSON;
          } else {
            res = xhr.status + ' ' + xhr.statusText;
          }
          $(ev.target).find('.panel-result').jsonViewer(res, {collapsed: true, rootCollapsable: false});
        },
      });
    },

    // 过滤空值
    filter(form) {
      if (Object.keys(form).length > 0) {
        for(var key in form) {
          if (!form[key]) {
            delete form[key];
          }
        }
      }
      return form;
    },

    // 签名
    sign(form) {
      return md5(md5(form) + this.key);
    },
  }
})
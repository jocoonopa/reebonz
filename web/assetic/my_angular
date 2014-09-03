'use strict';

/* App Module */

var myApp = angular.module('myApp', [
  'ngRoute',
  'ngAnimate',
  'ngSanitize', // for ngBindhtml
  'backendServices',
  'backendCtrls',
  'blockUI',
  'angularFileUpload',
  'ui.bootstrap'
]);

myApp.config(['$httpProvider', function ($httpProvider) {

  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

  /**
   * The workhorse; converts an object to x-www-form-urlencoded serialization.
   * @param {Object} obj
   * @return {String}
   */ 
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

    for(name in obj) {
      value = obj[name];

      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }

    return query.length ? query.substr(0, query.length - 1) : query;
  };

  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
}]).
config(['$routeProvider', function ($routeProvider) {
  $routeProvider.
    when('/backend', {
      templateUrl: Routing.generate('backend_index_body'),
      controller: 'BackendCtrl'
    }).
    when('/brand', {
      templateUrl: Routing.generate('brand_index'),
      controller: 'BrandCtrl'
    }).
    when('/pattern', {
      templateUrl: Routing.generate('pattern_index'),
      controller: 'PatternCtrl'
    }).
    when('/goodsLevel', {
      templateUrl: Routing.generate('goodsLevel_index'),
      controller: 'GoodsLevelCtrl'
    }).
    when('/goodsSource', {
      templateUrl: Routing.generate('goodsSource_index'),
      controller: 'GoodsSourceCtrl'
    }).
    when('/goodsMt', {
      templateUrl: Routing.generate('goodsMT_index'),
      controller: 'GoodsMtCtrl'
    }).
    when('/color', {
      templateUrl: Routing.generate('color_index'),
      controller: 'ColorCtrl'
    }).
    when('/supplier', {
      templateUrl: Routing.generate('supplier_index'),
      controller: 'SupplierCtrl'
    }).
    when('/goodsPassport', {
      templateUrl: Routing.generate('goodsPassport_index'),
      controller: 'GoodsPassportCtrl'
    }).
    when('/user', {
      templateUrl: Routing.generate('user_index'),
      controller: 'UserCtrl'
    }).
    when('/custom', {
      templateUrl: Routing.generate('custom_index'),
      controller: 'CustomCtrl'
    }).
    when('/normal', {
      templateUrl: Routing.generate('orders_normal'),
      controller: 'OrdersNormalCtrl'
    }).
    when('/move', {
      templateUrl: Routing.generate('move_index'),
      controller: 'MoveCtrl'
    }).
    when('/special/:id', {
      templateUrl: Routing.generate('orders_special'),
      controller: 'OrdersSpecialCtrl'
    }).
    when('/orders', {
      templateUrl: Routing.generate('orders_index'),
      controller: 'OrdersCtrl'
    }).
    when('/activity', {
      templateUrl: Routing.generate('activity_index'),
      controller: 'ActivityCtrl'
    }).
    when('/activity/:id/platform', {
      templateUrl: function (params) { 
        return Routing.generate('activity_platform', {id: params.id });
      },
      controller: 'ActivityPlatformCtrl'
    }).
    // when('/pay_type', {
    //   templateUrl: Routing.generate('payType_index'),
    //   controller: 'PayTypeCtrl'
    // }).
    // when('/exchange_rate', {
    //   templateUrl: Routing.generate('exchangeRate_index'),
    //   controller: 'ExchangeRateCtrl'
    // }).
    otherwise({
      redirectTo: '/backend'
    });
}]);

'use strict';

/* Directives */


angular.module('myApp.directives', []).
  directive('appVersion', ['version', function(version) {
    return function(scope, elm, attrs) {
      elm.text(version);
    };
  }]);

myApp.directive('myScrollTop', function () {
  return function (scope, element) {
    element.click(function (e) {
      e.preventDefault();
      $('html,body').animate({scrollTop: 0}, 350);
    }); 
  }
});

myApp.directive('myDatepicker', function () {
  return function (scope, element) {
    element.datepicker({
      format: 'yyyy-mm-dd',
      todayBtn: 'linked',
      language: 'zh-TW',
      todayHighlight: true
    });
  }
});

myApp.directive('myA', function() {
  return {
    restrict: 'E',
    link: function(scope, elem, attrs) {
      if(attrs.ngClick || attrs.href === '' || attrs.href === '#'){
        elem.on('click', function(e){
          e.preventDefault();
        });
      }
    }
  };
});

myApp.directive('myFileInput', function () {
  return {
    restrict: 'A',
    link: function (scope, elem) {
      elem.on('click', function (e) {
        $(this).siblings('input[type="file"]').click();
      });
    }
  }
});

myApp.directive('myEnter', function () {
  return function (scope, element, attrs) {
    element.bind("keydown keypress", function (event) {
      if(event.which === 13) {//alert('123');
        scope.$apply(function (){
          scope.$eval(attrs.myEnter);
        });

        event.preventDefault();
      }
    });
  };
});

myApp.directive('bindOnce', function() {
  return {
    scope: true,
    link: function( $scope ) {
      setTimeout(function() {
        $scope.$destroy();
      }, 0);
    }
  }
});

myApp.directive('goodsListTitle', function () {
  return {
    restrict: 'E',
    scope: {
      goods: '=',
      allow: '='
    },
    controller: function ($scope) {
      $scope.switchDisplay = function (goods) {
        goods.isDisplay = !goods.isDisplay;
      };

      $scope.lazyImg = function (goods) {
        goods.imgpathLazy = goods.imgpath;
      };
    },
    templateUrl: Routing.generate('goods_partials_listTitle')
  };
});

myApp.directive('myFormSave', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      click: '&onClick',
      delete: '&onDelete',
      move: '&onMove',
      orders: '&loadOrders'
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'save'})
  };
});

myApp.directive('myFormSelect', function () {
  return {
    restrict: 'E',
    scope: {
      entitys: '=',
      model: '=',
      name: '=',
      label: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'select'})
  };
});

myApp.directive('myFormInput', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'input'})
  };
});

myApp.directive('myFormDate', [ '$filter', function ($filter) {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      format: '=',
      label: '='
    },
    link: function (scope, elem, attrs) {
      return scope.model[scope.name] = $filter('date')(scope.model[scope.name], (scope.format) ? scope.format: 'yyyy-MM-dd HH:mm:ss');
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'date'})
  };
}]);

myApp.directive('myFormRadio', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '=',
      namePrefix: '=',
      radios: '=',
      disabled: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'radio'})
  };
});

myApp.directive('myFormSpan', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '=',
      spanClass: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'span'})
  };
});

myApp.directive('myFormStatusSwitch', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '=',
      prefix: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'status.switch'})
  };
});

myApp.directive('myFormTextarea', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '='
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'textarea'})
  };
});

myApp.directive('myFormImg',['$timeout', function ($timeout) {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '=',
      file: '=',
      click: '&onClick'
    },
    link: function (scope, elem, attrs) {
      elem.find('[type="file"]').on('change', function (evt) {
        // FileList object
        var files = evt.target.files; 

        // 會被傳送到 server
        scope.model.files = files;

        for (var i = 0, f; f = files[i]; i++) {
          // Only process image files.
          if (!f.type.match('image.*')) {
            continue;
          }

          var reader = new FileReader();

          // Read in the image file as a data URL.
          reader.readAsDataURL(f);

          // Closure to capture the file information.
          reader.onload = (function(theFile) {
            return function(e) {
              // I/O async problem prevent
              $timeout(function() {
                scope.model[scope.name] = e.target.result;                
              });
            };
          })(f);
        }
      });
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'img'})
  };
}]);

myApp.directive('myFormNumber', function () {
  return {
    restrict: 'E',
    scope: {
      model: '=',
      name: '=',
      label: '=',
      isPrice: '=',
      isRequired: '=',
      change: '&onChange'
    },
    link: function (scope) {
      scope.$watch('model[name]', function(newValue, oldValue) {
        var arr = String(newValue).split("");

        if (arr.length === 0) {
          return;
        }

        if (arr.length === 1 && (arr[0] == '-' || arr[0] === '.' )) {
          return;
        }

        if (arr.length === 2 && newValue === '-.') {
          return;
        }

        if (isNaN(newValue)) {
          scope.model[scope.name] = oldValue || '';
        }

        scope.model[scope.name] = (scope.isPrice) ? scope.model[scope.name] : Math.abs(scope.model[scope.name]); 
      });
    },
    templateUrl: Routing.generate('goods_partials_form', {type: 'number'}),
  };
});

myApp.directive('msgFooter', function () {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      success: '=',
      error: '=',
      empty: '&onEmpty'
    },
    templateUrl: Routing.generate('backend_partials_msgFooter')
  };
});

myApp.directive('ngConfirmClick', [
  function(){
    return {
      link: function (scope, element, attr) {
        var msg = attr.ngConfirmClick || "Are you sure?";
        var clickAction = attr.confirmedClick;
        element.bind('click',function (event) {
          if ( window.confirm(msg) ) {
            scope.$eval(clickAction)
          }
        });
      }
    };
}]);

myApp.directive('myOpeList', [ '$filter',
  function ($filter) {
    return {
      restrict: 'E',
      scope: {
        orders: '='
      },
      controller: function ($scope) {
        /**
         * 格式化時間
         * 
         * @param  {string} date   
         * @param  {string} format [example: 'yyyy-mm-dd']
         * @return {string} [格式化以後的時間]
         */
        $scope.formatDate = function (date, format) {
          var format = format || 'yyyy-MM-dd';

          return $filter('date')(date, format);
        };
      },
      templateUrl: Routing.generate('ope_partials_list')
    };
}]);

'use strict';

/* Services */

var backendServices = angular.module('backendServices', ['ngResource']);

backendServices.factory('Brand', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_brand_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('Pattern', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_pattern_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('Color', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_color_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('GoodsLevel', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_goodsLevel_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('GoodsSource', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_goodsSource_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Supplier', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_supplier_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('PayType', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_payType_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('GoodsMT', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_goodsMT_list') + '/:id', null, 
    {
    	update: { method: 'PUT'}
    });
}]);

backendServices.factory('GoodsStatus', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_goodsStatus_list') + '/:id');
}]);

backendServices.factory('OrdersStatus', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_ordersStatus_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('OrdersKind', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_ordersKind_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Store', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_store_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('User', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_user_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Role', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_role_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Custom', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_custom_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('PayType', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_payType_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Move', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_move_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

backendServices.factory('Activity', ['$resource',
  function ($resource) {
  return $resource(Routing.generate('api_activity_list') + '/:id', null, 
    {
      update: { method: 'PUT'}
    });
}]);

'use strict';

/* Controllers */

var backendCtrls = angular.module('backendCtrls', []);

// Please note that $modalInstance represents a modal window (instance) dependency.
// It is not the same as the $modal service used above.

var ModalInstanceCtrl = function ($scope, $modalInstance, $filter, orderses) {

  $scope.orderses = orderses;

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };

  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };
};

'use strict';

/* Controllers */

backendCtrls.controller('ActivityCtrl', ['$scope', '$routeParams', '$filter', '$http', 'Activity',
  function ($scope, $routeParams, $filter, $http, Activity) { 
    var GS_ONSALE = 1;
    var GS_ACTIVITY = 6;

    var init = function () {
      $scope.activitys = Activity.query();
      
      $scope.newActivity = {};

      setNull($scope.newActivity);
    };

    /**
     * 取得目前的時間，並且格式化為 yyyy-mm-dd
     * 
     * @return {string}
     */
    var getToday = function () {
      var today = new Date();

      return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    };

    var setNull = function (activity) {
      activity.exceed = 0;
      activity.minus = 0;
      activity.name = '';
      activity.description = '';
      activity.discount = 0;
      activity.start_at = getToday();
      activity.end_at = getToday();
    };

    var isSuccess = function (msg) {
      $scope.success = msg;
      $scope.error = false;
    };

    var isError = function (msg) {
      $scope.success = false;
      $scope.error = msg;
    };

    $scope.emptyMsg = function () {
      $scope.success = null;
      $scope.error = null;
    };

    $scope.formatDate = function (date, format) {
      var format = format || 'yyyy-MM-dd';
      return $filter('date')(date, format).substring(0, 10);
    };

    $scope.isDiscount = function (activity) {
      return (activity.discount > 0);
    };

    $scope.isGiftWithPurchase = function (activity) {
      return (activity.exceed > 0 &&  activity.minus > 0);
    };

    $scope.switchDisplay = function (activity) {
      activity.isDisplay = !activity.isDisplay;
    };

    $scope.preventBubble = function ($event) {
      if ($event.stopPropagation) {
        $event.stopPropagation();
      }
    };

    $scope.show = function (activity) {
      Activity.get({id: activity.id}).
        $promise.then(function (res) {
          activity = res;
        }, function () {
          isError('取得活動資料失敗!');
        });
    };

    $scope.create = function (activity) {
      Activity.save(activity).
        $promise.then(function (activity) {
          isSuccess(activity.name + '新增完成!');
          $scope.activitys.push(activity);
        }, function () {
          isError(activity.name + '新增失敗!');
        });
    };

    $scope.update = function (activity) {
      Activity.update({id: activity.id}, activity).
        $promise.then(function (res) {
          isSuccess(activity.name + '修改完成!');
          activity = '';
        }, function () {
          isError(activity.name + '修改失敗!');
        });
    };

    $scope.destroy = function (activity) {
      Activity.delete({id: activity.id}, activity).
        $promise.then(function (res) {
          activity.isDelete = true;
          isSuccess(activity.name + '刪除完成');
        }, function () {
          isError(activity.name + '刪除失敗，請檢查是否有相關商品');
        });
    };

    $scope.reset = function (activity) {
      setNull(activity);

      activity.start_at = getToday();
      activity.end_at = getToday();
    };

    init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('ActivityPlatformCtrl', ['$scope', '$routeParams', '$filter', '$http', 'Activity',
  function ($scope, $routeParams, $filter, $http, Activity) {
    var GS_ONSALE = 1;
    var GS_ACTIVITY = 6;

    var condition = {
      Gactivity: {
        in: [$routeParams.id]
      }
    };

    var orderBy = {
      attr: 'update_at',
      dir: 'DESC'
    };

    var initPost = function (barcode) {
      var post = {
        Gstatus: {
          in: [GS_ONSALE, GS_ACTIVITY]
        }
      };

      if (barcode) {
        post.Gsn = {};
        post.Gsn.in = [barcode];
      }

      return post;
    };

    var setSuccess = function (msg) {
      $scope.success = msg;
      $scope.error = null;
    };

    var setError = function (msg) {
      $scope.success = null;
      $scope.error = msg;
    };

    var _push = function () {
      var goodsPosts = [];

      for (var key in $scope.list) {
        goodsPosts.push($scope.list[key]);
      }

      $http.put(Routing.generate('api_activity_push', {id: $routeParams.id}), {goodsPost: goodsPosts})
        .success(function (res) {
          if (res.status === 'ok') {
            $scope.init();
            setSuccess('刷出活動完成!');
          }
        }).error(function (res) {
          setError('刷出活動發生錯誤!');
        });
    };

    var pull = function () {
      var goodsPosts = [];

      for (var key in $scope.list) {
        goodsPosts.push($scope.list[key]);
      }

      $http.put(Routing.generate('api_activity_pull', {id: $routeParams.id}), {goodsPost: goodsPosts})
        .success(function (res) {
          if (res.status === 'ok') {
            $scope.init();
            setSuccess('刷入活動完成!');
          }
        }).error(function (res) {
          setError('刷入活動發生錯誤!');
        });
    };

    var get = function (barcode) {
      for (var key in $scope.list) {
        if ($scope.list[key].sn === barcode) {
          return;
        }
      }

      var post = initPost(barcode);

      $http.get(Routing.generate('api_goodsPassport_filter', {jsonCondition: JSON.stringify(post)}))
        .success(function (goodsGroup) {
          if (goodsGroup.length === 0) {
            setError(barcode + '資料無法取得，請確認該商品是否為上架或活動狀態!');
            return;
          }

          var goods = goodsGroup[0];

          $scope.list.push({id: goods.id, sn: goods.sn});
          $scope.barcode = null;

          setSuccess(goods.sn + '資料取得成功，目前狀態為' + goods.status.name + '!');
        })
        .error(function (e) {
          console.log('error');
          setError(barcode + '資料取得發生錯誤!');
        }); 
    };

    $scope.emptyMsg = function () {
      $scope.success = null;
      $scope.error = null;
    };

    $scope.formatDate = function (date, format) {
      var format = format || 'yyyy-MM-dd';
      return $filter('date')(date, format);
    };

    $scope.init = function () {
      $scope.activity = Activity.get({id: $routeParams.id });
      $scope.soldCount = 0;
      $scope.barcode = null;
      $scope.act = 1;
      $scope.list = [];
      $scope.totalItems = 0;
      $scope.currentPage = 1;
      $scope.perPage = 10;
      $scope._query();
      $scope.query = {sn: ''};
      $scope.success = null;
      $scope.error = null;
      $scope.pageInit();
    };

    $scope.emptyMsg = function () {
      $scope.success = null;
      $scope.error = null;
    };

    $scope._query = function () {
      $http.get(Routing.generate('api_goodsPassport_filter', {jsonCondition: JSON.stringify(condition), jsonOrderBy: JSON.stringify(orderBy), page: $scope.currentPage, perPage: $scope.perPage}))
      .success(function (data) {
        $scope.goodses = data;

        for (var key in $scope.goodses) {
          if ($scope.goodses[key].status.id === 2 ) {
            $scope.soldCount ++;
          }
        }
      });
    };

    /**
     * 換頁時觸發動作，這邊是執行 query() 方法取得資料
     */
    $scope.pageChanged = function() {
      $scope._query();
    };

    $scope.setPage = function (pageNo) {
      $scope.currentPage = pageNo;
    };

    /**
     * 初始化頁籤
     */
    $scope.pageInit = function () {
      $http.get(Routing.generate("api_goodsPassport_filter_count", {jsonCondition: JSON.stringify(condition)})).
        success(function (total) {
          $scope.totalItems = total;
        });
    };

    $scope.saveList = function () {
      switch ($scope.act)
      {
        case 0:
          _push();
          break;

        case 1:
          pull();
          break;

        default:
          break;
      }
    };

    $scope.removeList = function (index) {
      $scope.list.splice(index, 1);
    };

    $scope.emptyList = function () {
      var r = confirm('確定清空嘛?');
      
      return (r) ? $scope.list = [] : false;
    }

    $scope.add = function () {
      get($scope.barcode);
    };

    /**
     * 匯出檔案
     */
    $scope.export = function () {    
      window.location = Routing.generate("api_goodsPassport_export", {jsonCondition: JSON.stringify(condition)});
    };

    $scope.init();
}]);
'user strict';

backendCtrls.controller('BackendCtrl', [ function () {}]);
'use strict';

backendCtrls.controller('BrandCtrl', ['$scope', '$routeParams', '$http', 'Brand',
	function ($scope, $routeParams, $http, Brand) { 

  $scope.init = function () {
    $scope.brands = Brand.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
  	Brand.save(query).
	  	$promise.then(function () {
	  		$scope.successMsg = query.name + ' 新增完成!';
	  		$scope.brands = Brand.query();
        $scope.errorMsg = null;
	  		$scope.query.name = '';
	  	}, function (error) {
        $scope.successMsg = null;
	  		$scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
	  	});
  };

  $scope.update = function (brand) {

  	if (brand.name === $scope.tmp.name) {
  		return;
  	}

  	Brand.update({ id: brand.id}, brand).
  		$promise.then(function () {
  			$scope.successMsg = brand.name + ' 修改完成!';
  			//$scope.brands = Brand.query();
  			$scope.query.name = '';
  		}, function () {
  			$scope.errorMsg = brand.name + '修改失敗，請確認是否有名稱重複!';
  		});
  };

  $scope.destroy = function (brand) {
  	Brand.delete({ id: brand.id}).
  		$promise.then(function () {
  			$scope.successMsg = brand.name + ' 刪除完成!';
  			$scope.brands = Brand.query();
  			$scope.query.name = '';
  		}, function (e) {
  			$scope.errorMsg = brand.name + '刪除失敗，請確認是否有綁定資料!';
  		});
  };

  $scope.clean = function () {
  	$scope.successMsg = false;
  	$scope.errorMsg = false;
  };

  $scope.setTmp = function (brand) {
  	$scope.tmp.id = brand.id;
  	$scope.tmp.name = brand.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('ColorCtrl', ['$scope', '$routeParams', '$http', 'Color',
  function ($scope, $routeParams, $http, Color) { 

  $scope.init = function () {
    $scope.colors = Color.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
    Color.save(query).
      $promise.then(function () {
        $scope.successMsg = query.name + ' 新增完成!';
        $scope.colors = Color.query();
        $scope.query.name = '';
      }, function (error) {
        $scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
      });
  };

  $scope.update = function (color) {

    if (color.name === $scope.tmp.name) {
      return;
    }

    Color.update({ id: color.id}, color).
      $promise.then(function () {
        $scope.successMsg = color.name + ' 修改完成!';
        $scope.errorMsg = null;
        //$scope.colors = Color.query();
        $scope.query.name = '';
      }, function () {
        $scope.successMsg = null;
        $scope.errorMsg = color.name + '修改失敗，請確認是否有名稱重複!';
      });
  };

  $scope.destroy = function (color) {
    Color.delete({ id: color.id}).
      $promise.then(function () {
        $scope.successMsg = color.name + ' 刪除完成!';
        $scope.colors = Color.query();
        $scope.query.name = '';
      }, function (e) {
        $scope.errorMsg = color.name + '刪除失敗，請確認是否有綁定資料!';
      });
  };

  $scope.clean = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  };

  $scope.setTmp = function (color) {
    $scope.tmp.id = color.id;
    $scope.tmp.name = color.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('CustomCtrl', ['$scope', '$routeParams', '$http', '$filter','Custom',
  function ($scope, $routeParams, $http, $filter, Custom) { 

  var isSuccess = function (custom) {
    $scope.successMsg = '';
    $scope.successMsg += custom.name + ' 新增完成';
    $scope.errorMsg = false;
  };

  var isFailed = function () {
    $scope.errorMsg = ($scope.custom.name) ? $scope.custom.name + '新增失敗' : '新增失敗';
    $scope.successMsg = false;
  };

  $scope.initData = function () {
    $scope.query = {};
    $scope.query.store = {};
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通

    $scope.customs = Custom.query(function (customs) {
      for (var key in customs) {
        customs[key].birthday = $filter('date')(customs[key].birthday, 'yyyy-MM-dd');
      }
    });

    $scope.query.store.id = 1;

    $scope.successMsg = false;
    $scope.errorMsg = false;
    
    $scope.stores = $('select[name="store"]').data('options');
  };

  $scope.save = function () {   
    $scope.clean();

    $http.post(Routing.generate("api_custom_create"), $scope.query).
      success(function (custom) {
        $scope.initData();
        isSuccess(custom);
      })
      .error(function (e) {
        isFailed();
        console.log(e);
      });
  };

  $scope.update = function (custom) {
    if (
      ($scope.tmp.id === custom.id) &&
      ($scope.tmp.name === custom.name) &&
      ($scope.tmp.memo === custom.memo) &&
      ($scope.tmp.birthday === custom.birthday) &&
      ($scope.tmp.sex === custom.sex) &&
      ($scope.tmp.email === custom.email) &&
      ($scope.tmp.mobil === custom.mobil) &&
      ($scope.tmp.address === custom.address) &&
      ($scope.tmp.store === custom.store) 
    ) {
      return;
    }

    $scope.clean();

    Custom.update({ id: custom.id}, custom).
      $promise.then(function () {
        $scope.successMsg = custom.name + ' 修改完成!';
        //$scope.customs = Custom.query();
      }, function (e) {
        $scope.errorMsg = custom.name + '修改失敗，請確認是否有名稱重複!';
        console.log(e);
      });
  };

  $scope.destroy = function (custom) {
    
    $scope.clean();

    Custom.delete({ id: custom.id}).
      $promise.then(function () {
        $scope.successMsg = custom.name + ' 刪除完成!';
        $scope.customs = Custom.query();
      }, function (e) {
        $scope.errorMsg = custom.name + '刪除失敗，請確認是否有綁定資料!';
        console.log(e);
      });
  };

  $scope.clean = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  };

  $scope.setTmp = function (custom) {
    
    $scope.tmp.id = custom.id;
    $scope.tmp.name = custom.name;
    $scope.tmp.memo = custom.memo;
    $scope.tmp.birthday = custom.birthday;
    $scope.tmp.sex = custom.sex;
    $scope.tmp.email = custom.email;
    $scope.tmp.mobil = custom.mobil;
    $scope.tmp.address = custom.address;
    $scope.tmp.store = custom.store;
  };

  $scope.initData();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsLevelCtrl',['$scope', '$routeParams', '$http', 'GoodsLevel',
	function ($scope, $routeParams, $http, GoodsLevel) { 

  $scope.init = function () {
    $scope.goodsLevels = GoodsLevel.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
  	GoodsLevel.save(query).
	  	$promise.then(function () {
	  		$scope.successMsg = query.name + ' 新增完成!';
	  		$scope.goodsLevels = GoodsLevel.query();
	  		$scope.query.name = '';
	  	}, function (error) {
	  		$scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
	  	});
  };

  $scope.update = function (goodsLevel) {

  	if (goodsLevel.name === $scope.tmp.name) {
  		return;
  	}

  	GoodsLevel.update({ id: goodsLevel.id}, goodsLevel).
  		$promise.then(function () {
  			$scope.successMsg = goodsLevel.name + ' 修改完成!';
  			//$scope.goodsLevels = GoodsLevel.query();
  			$scope.query.name = '';
  		}, function () {
  			$scope.errorMsg = goodsLevel.name + '修改失敗，請確認是否有名稱重複!';
  		});
  };

  $scope.destroy = function (goodsLevel) {
  	GoodsLevel.delete({ id: goodsLevel.id}).
  		$promise.then(function () {
  			$scope.successMsg = goodsLevel.name + ' 刪除完成!';
  			$scope.goodsLevels = GoodsLevel.query();
  			$scope.query.name = '';
  		}, function (e) {
  			$scope.errorMsg = goodsLevel.name + '刪除失敗，請確認是否有綁定資料!';
  		});
  };

  $scope.clean = function () {
  	$scope.successMsg = false;
  	$scope.errorMsg = false;
  };

  $scope.setTmp = function (goodsLevel) {
  	$scope.tmp.id = goodsLevel.id;
  	$scope.tmp.name = goodsLevel.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsMtCtrl', ['$scope', '$routeParams', '$http', 'GoodsMT',
  function ($scope, $routeParams, $http, GoodsMT) { 

  $scope.init = function () {
    $scope.goodsMTs = GoodsMT.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
    GoodsMT.save(query).
      $promise.then(function () {
        $scope.successMsg = query.name + ' 新增完成!';
        $scope.goodsMTs = GoodsMT.query();
        $scope.query.name = '';
      }, function (error) {
        $scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
      });
  };

  $scope.update = function (goodsMT) {

    if (goodsMT.name === $scope.tmp.name) {
      return;
    }

    GoodsMT.update({ id: goodsMT.id}, goodsMT).
      $promise.then(function () {
        $scope.successMsg = goodsMT.name + ' 修改完成!';
        //$scope.goodsMTs = GoodsMT.query();
        $scope.query.name = '';
      }, function () {
        $scope.errorMsg = goodsMT.name + '修改失敗，請確認是否有名稱重複!';
      });
  };

  $scope.destroy = function (goodsMT) {
    GoodsMT.delete({ id: goodsMT.id}).
      $promise.then(function () {
        $scope.successMsg = goodsMT.name + ' 刪除完成!';
        $scope.goodsMTs = GoodsMT.query();
        $scope.query.name = '';
      }, function (e) {
        $scope.errorMsg = goodsMT.name + '刪除失敗，請確認是否有綁定資料!';
      });
  };

  $scope.clean = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  };

  $scope.setTmp = function (goodsMT) {
    $scope.tmp.id = goodsMT.id;
    $scope.tmp.name = goodsMT.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsImportCtrl', ['$scope', '$routeParams', '$http', '$timeout', '$upload', '$filter',
  function ($scope, $routeParams, $http, $timeout, $upload, $filter) {
  /**
   * 檔案選擇時，馬上顯示圖片
   */
  $scope.onFileSelect = function ($files, type) {
    $scope[type] = $files;
    
    // 取出第0個元素
    var $file = $files[0];
    
    // 這段不是很懂... 總之就是將上傳的圖片透過 HTML5 的 API 立即顯示
    if (window.FileReader && $file.type.indexOf('image') > -1) {
      var fileReader = new FileReader();
      
      fileReader.readAsDataURL($files[0]);
      
      var loadFile = function(fileReader) {
        fileReader.onload = function(e) {
          $timeout(function() {
            if (goods) {
              goods.imgpath = e.target.result;
            }
          });
        }
      }(fileReader);
    }
  };

  /**
   * 批次上傳商品(匯入 excel )
   */
  $scope.import = function () {
    if (!$scope.importfiles) {
      return;
    }

    for (var i = 0; i < $scope.importfiles.length; i++) {
      var file = $scope.importfiles[i];

      $scope.upload = $upload.upload({
        url: Routing.generate('api_goodsPassport_import'),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj}, // 這行坦白說我不知道意思@@
        file: file, 
      }).success(function(data, status, headers, config) {
        if (data.error) {
          var msg = data.error;

          $scope.errorEntity = {};
          $scope.errorEntity.resource = data.resource;
          $scope.errorEntity.name = data.name;
          
          return $scope.isError(msg);
        }

        $scope.errorEntity = false;

        for (var key in data) {
          // 防止空實體造成錯誤
          $scope.preventEntityError(data[key]);

          // 將取得的資料放入scope
          $scope.importGoods.push(data[key]);
        }
        // 清除要上傳的檔案
        $scope.cleanFile('#import-file');

        $scope.isSuccess('批次上傳成功');
      }).error(function (e) {
        $scope.errorEntity = false;
        $scope.isError('批次上傳失敗');
      });
    }
  };

  $scope.addEntity = function () {
    $http.post(Routing.generate('api_' + $scope.errorEntity.resource + '_create'), {name: $scope.errorEntity.name})
      .success(function (res) {
        $scope.isSuccess(res.name + '新增完成!');
        $scope.errorEntity = false;
      })
      .error(function () {
        $scope.isError($scope.errorEntity.name + '新增失敗!');
      });
  };
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsKeyInCtrl', ['$scope', '$routeParams', '$http', '$timeout', '$upload', '$filter',
  function ($scope, $routeParams, $http, $timeout, $upload, $filter) { 
  /**
   * 取得目前的時間，並且格式化為 yyyy-mm-dd
   * 
   * @return {string}
   */
  var getToday = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
  };

  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 將圖片檔案透過 ajax 傳送到 server 儲存
   */
  $scope.uploadMulti = function ($files, ids, goodses) {
    if (!$files) {
      return;
    }

    //$files: an array of files selected, each file has name, size, and type.
    for (var i = 0; i < $files.length; i++) {
      var file = $files[i];

      $scope.upload = $upload.upload({
        url: Routing.generate('img_upload_multi', {ids: JSON.stringify(ids)}),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj},
        file: file, 
      }).success(function (data, status, headers, config) {
        for (var key in goodses) {
          goodses[key].imgpath = data;
        }

        // 加入成功訊息
        $scope.isSuccess('新增成功');
      }).error(function (e) {
        $scope.isError('圖片上傳失敗');
      });
    }
  };

  // 一般新增
  $scope.save = function ($files) {    
    $http.post(Routing.generate("api_goodsPassport_create"), $scope.goods).
      success(function (goods) {    
        if (goods.error) {
          return $scope.isError(goods.error);
        }

        // 上傳圖片會用到
        var ids = [];

        // 迴圈處理時間格式資料
        for (var key in goods) {
          // 防止空實體造成錯誤
          $scope.preventEntityError(goods[key]);

          // 加入新增成功的陣列
          $scope.addGoods.push(goods[key]);

          // 加入ids
          ids.push(goods[key].id);
        }

        // 上傳圖片
        $scope.uploadMulti($files, ids, $scope.addGoods);

        $scope.isSuccess('新增商品完成!');
      })
      .error(function (e) {
        $scope.isError('新增失敗!');
      });
  };

  // 手動上傳完成後將表單相關的變數還原
  $scope.initKeyInForm = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.goods = {};
    $scope.goods.amount = 1;
    $scope.goods.imgpath = $scope.img404;
    $scope.goods.allow_discount = 1;
    $scope.goods.in_type = 0;
    $scope.goods.is_web = 1;
    $scope.goods.purchase_at = getToday();
    $scope.goods.paid = 0;
    $scope.goods.feedback = 0;
    $scope.goods.pattern = {id: ''};
    $scope.goods.source = {id: ''};
    $scope.goods.brand = {id: ''};
    $scope.goods.level = {id: ''};
    $scope.goods.mt = {id: ''};
    $scope.goods.supplier = {id: 1};
    $scope.goods.color = {id: ''};

    $http.get(Routing.generate('api_user_current'))
      .success(function (user) {
        $scope.goods.store = user.store;
      })
      .error(function (e) {
        console.log(e);

        $scope.goods.store = {};
      });
  };

  $scope.initKeyInForm();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsPassportCtrl', [ '$scope', '$http', '$filter', '$timeout', '$upload', '$sce', '$modal', '$log', 'Brand', 'Pattern', 'Color', 'GoodsLevel', 'GoodsSource', 'Supplier', 'PayType', 'GoodsMT', 'GoodsStatus', 'Store', 'Move', 'Activity',
 function ($scope, $http, $filter, $timeout, $upload, $sce, $modal, $log, Brand, Pattern, Color, GoodsLevel, GoodsSource, Supplier, PayType, GoodsMT, GoodsStatus, Store, Move, Activity) { 
  
  $scope.initMeta = function() {
    $scope.entity = {};
    $scope.entity.brands = Brand.query();
    $scope.entity.patterns = Pattern.query();
    $scope.entity.colors = Color.query();
    $scope.entity.suppliers = Supplier.query();
    $scope.entity.mts = GoodsMT.query();
    $scope.entity.activitys = Activity.query();
    $scope.entity.goodsSources = GoodsSource.query();
    $scope.entity.goodsLevels = GoodsLevel.query();
    $scope.entity.stores = Store.query();
    $scope.entity.goodsStatuss = GoodsStatus.query();
    $scope.entity.isWebRadios = [{text: '是', value: 1}, {text: '否', value: 0}];
    $scope.entity.isAllowDiscountRadios = [{text: '是', value: 1}, {text: '否', value: 0}];
    $scope.entity.isInTypeRadios = [{text: '一般', value: 0}, {text: '寄賣', value: 1}];
    $scope.img404 = '/img/404.png';
    $scope.importGoods = []; // 批次上傳後回傳的商品資料
    $scope.addGoods = [];// 單筆上傳後回傳的商品資料( 回傳為陣列特別注意!!! )
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.globalOrders = []; // 訂單modal要用的變數
  };

  $scope.goSell = function () {
    window.location.href='#/normal';
  };

  $scope._formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 檔案選擇時，馬上顯示圖片
   */
  $scope.onFileSelect = function ($files, type, goods) {
    if (goods) {
      goods[type] = $files;
    } else {
      $scope[type] = $files;
    }
    
    // 取出第0個元素
    var $file = $files[0];
    
    // 這段不是很懂... 總之就是將上傳的圖片透過 HTML5 的 API 立即顯示
    if (window.FileReader && $file.type.indexOf('image') > -1) {
      var fileReader = new FileReader();
      
      fileReader.readAsDataURL($files[0]);
      
      var loadFile = function(fileReader) {
        fileReader.onload = function(e) {
          $timeout(function() {
            if (goods) {
              goods.imgpath = e.target.result;
            }
          });
        }
      }(fileReader);
    }
  };

  /**
   * Lazy load 圖片(展開商品資料時才讀取)
   */
  $scope.setLazyImg = function (goods) {
    goods.imgpathLazy = goods.imgpath;
  };

  /**
   * 移除圖片
   */
  $scope.removeImg = function (goods) {
    goods.imgpath = $scope.img404;
    $scope.setLazyImg(goods);
  };

  $scope.isSuccess = function (msg, goods) {
    $scope.successMsg = (goods) ?  goods.name + '(' + goods.sn + ')' + msg : '' + msg;
    $scope.successMsg = $sce.trustAsHtml($scope.successMsg);
    $scope.errorMsg = false;
  };

  $scope.isError = function (msg, goods) {
    $scope.errorMsg = (goods) ? goods.name + '(' + goods.sn + ')' + msg : '' + msg;
    $scope.errorMsg = $sce.trustAsHtml($scope.errorMsg);
    $scope.successMsg = false;
  };

  $scope.emptyMsg = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  }

  // 清除上傳的圖片或檔案
  $scope.cleanFile = function (selector) {
    $(selector).val('');// 很無奈的只能先用jQuery處理了，angualr 對file 的支援好少喔
  };

  $scope.delete = function (goods) {
    $http.delete(Routing.generate('api_goodsPassport_delete', {id: goods.id}), goods).
      success(function (res) {
      goods.isDelete = true;
      $scope.isSuccess('刪除完成', goods);
    });
  };

  $scope.splice = function (repo, elem) {
    var index = repo.indexOf(elem);

    if (index !== -1) {
      repo.splice(index, 1);
    }
  };

  /**
   * 還原上傳操作
   */
  $scope.reverse = function (model, msg) {
    var postId = [];

    for (var key in $scope[model]) {
      postId.push($scope[model][key].id);
    }

    // 這段api 要小心用，其實就是把商品不留痕跡的抹除，這東西太危險目前限制只在還原時使用，平常商品刪除就只能通過destroy 或下架
    $http.delete(Routing.generate('api_goodsPassport_reverse', {jsonIds: JSON.stringify(postId)}))
      .success(function () {      
        $scope[model] = [];
        $scope.isSuccess(msg + '還原完成!');
      }).error(function () {
        $scope.isError(msg + '還原失敗!');
      });
  };

  $scope.put = function (index, repo) {
    var goods = repo[index];

    $http.put(Routing.generate("api_goodsPassport_update", {id: goods.id}), goods).
      success(function (returnGoods) {
        returnGoods.isSuccess = true;

        repo[index] = returnGoods;
        
        $scope.setLazyImg(goods);
        
        $scope.isSuccess('修改成功', goods);
      })
      .error(function (e) {
        $scope.isError('修改失敗', goods);
      });
  }

  /**
   * 更新商品資訊
   * @param  {integer} index 
   * @param  {array}
   */
  $scope.update = function (index, repo) {
    var goods = repo[index];
    var $files = goods.files;

    if (typeof $files === 'undefined' || !$files) {
      return $scope.put(index, repo);
    }

    for (var i = 0; i < $files.length; i++) {
      var file = $files[i];
      $scope.upload = $upload.upload({
        url: Routing.generate('img_upload', {id: goods.id}),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj},
        file: file, 
      }).progress(function(evt) { // 可以監聽上傳過程，不過這邊我們用不到就是
        //$scope.progress = parseInt(100.0 * evt.loaded / evt.total);
      }).success(function(data, status, headers, config) {
        goods.imgpath = data;
        $scope.put(index, repo);
      }).error(function (e) {
        $scope.isError('圖片修改失敗');
      });
    }
  };

  $scope.move = function (goods) {
    Move.save({id: goods.id}, function (res) {
      if (res.msg) {
        return $scope.isError(res.msg);
      }

      $scope.isSuccess('訂貨請求成功發送!');
    });
  };

  $scope.preventEntityError = function (model) {
    var r = ['brand', 'pattern', 'level', 'source', 'mt', 'supplier', 'color'];

    for (var i in r) {// 防止不存在實體引起錯誤
      if (typeof model[r[i]] === 'undefined') {
        model[r[i]] = {id: ''};
      }
    }
  };

  $scope.open = function (goods) {
    var post = {Gsn: {in: [goods.sn]}};

    $http.get(Routing.generate('api_orders_filter', {jsonCondition: JSON.stringify(post)}))
      .success(function (orderses) {
        var modalInstance = $modal.open({
          templateUrl: 'myModalContent.html',
          controller: ModalInstanceCtrl,
          size: false,
          resolve: {
            orderses: function () {
              return $scope.globalOrders = orderses;
            }
          }
        });

        $scope.isSuccess(false);
      }).error(function () {
        $scope.isError('訂單讀取發生錯誤!');
      });
  };

  $scope.initMeta();
}]);
'use strict';

/* Controllers */

/**
 * 商品搜尋及相關CRUD
 */
backendCtrls.controller('GoodsSearchCtrl', ['$scope', '$routeParams', '$http', '$upload', '$timeout', '$filter',
  function ($scope, $routeParams, $http, $upload, $timeout, $filter) { 
  
  var GoodsOperator;

  /**
   * 取得目前的時間，並且格式化為 yyyy-mm-dd
   * 
   * @return {string}
   */
  var getToday = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
  };
  
  /**
   * 初始化資料
   */
  $scope.initSearchMeta = function () {
    $scope.goods = {};
    $scope.repo = {};
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.queryDes = false;
    $scope.orderDir = 'ASC';
    $scope.totalItems = 0;
    $scope.currentPage = 1;
    $scope.perPage = 10;
    $scope.orderBy = {attr: 'id', name: '索引'};
    $scope.orderDir = 'DESC';
    $scope.searchRepo = [];
    $scope.actType = '';
    $scope.checkStatus = true;
    $scope.punchActivity = {
      description: "借出",
      discount: 0,
      end_at: "2016-04-09T00:00:00+0800",
      exceed: 0,
      id: 1,
      minus: 0,
      name: "借出",
      start_at: "2014-08-01T00:00:00+0800",
    };

    $scope.startAt = getToday();
    $scope.endAt = getToday();

    $scope.isAllowEdit = true;

    // 取得排序資料
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/goods/order_conditions.json').
      success(function (res) {
        $scope.orderBys = res;
      });

    // 取得 mapping 設定
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/mapping.json').
      success(function (res) {
        /**
         * 查詢描述英中對應陣列
         * @type {Object}
         */
        $scope.mapping = res;
      });

    GoodsOperator = new GoodsOperator;
  };

  $scope.addTimeCondition = function (type) {
    var attr = type;

    $scope.goods[attr] = $scope.startAt;
    $scope.addInSearchRePo(type, 'gte');

    $scope.goods[attr] = $scope.endAt;
    $scope.addInSearchRePo(type, 'lte');
  };

  $scope.checkAll = function () {
    for (var key in $scope.searchRepo) {
      $scope.searchRepo[key].isCheck = $scope.checkStatus;
    }
  };
  
  $scope.doAct = function () {
    GoodsOperator[$scope.actType]();
  };
 
  /**
   * 根據條件取得查詢結果
   */
  $scope.query = function () {
    $http.get(
      Routing.generate(
        "api_goodsPassport_filter", 
        {
          jsonCondition: JSON.stringify($scope.repo), 
          page: $scope.currentPage, 
          perPage: $scope.perPage,
          jsonOrderBy: JSON.stringify({attr: $scope.orderBy.attr, dir: $scope.orderDir})
        }
      )).
      success(function (goods) {
        for (var key in goods) {
          $scope.preventEntityError(goods[key]);

          $scope.switchPanelAndRes();
        }

        if (goods.length === 0) {
          $scope.switchPanelAndRes();
          $scope.isError('查無商品!');
        }

        $scope.searchRepo = goods;
      }).
      error(function () {
        $scope.isError('Woops! 查詢發生錯誤！');
      });
  };

  /**
   * 結果區域及輸入區域切換顯示
   */
  $scope.switchPanelAndRes = function () {
    $scope.isSearchPanelVisible = true;
    $scope.isSearchResVisible = false;
  };

  /**
   * 匯出檔案
   */
  $scope.export = function () {    
    window.location = Routing.generate("api_goodsPassport_export", {jsonCondition: JSON.stringify($scope.repo)});
  };

  /**
   * 查詢條件以及結果清空
   */
  $scope.reset = function () {
    $scope.repo = {};
    $scope.goods = {};
    $scope.queryDes = false;
  };

  /**
   * 初始化頁籤
   */
  $scope.pageInit = function () {
    $http.get(Routing.generate("api_goodsPassport_filter_count", {jsonCondition: JSON.stringify($scope.repo)})).
      success(function (total) {
        $scope.totalItems = total;
      });
  };

  /**
   * 換頁時觸發動作，這邊是執行 query() 方法取得資料
   */
  $scope.pageChanged = function() {
    $scope.query();
  };

  /**
   * 加入搜尋條件陣列
   * 
   * @param {string} attr [資料屬性]
   * @param {string type [邏輯類型{ notin, in, like ....}]
   */
  $scope.addInSearchRePo = function (attr, type) {
    if (!$scope.goods[attr]) {
      return;
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = [];
    }

    $scope.repo[attr][type].push($scope.goods[attr]);
    $scope.goods[attr] = '';
    $scope.buildQueryDes();
  };

  /**
   * 設置相等查詢條件
   * 
   * @param {string} attr [屬性]
   * @param {string} type [邏輯類別]
   */
  $scope.setEqRepo = function (attr, type) {
    if (!$scope.goods[attr]) {
      $scope.repo[attr] = false;

      return $scope.buildQueryDes();
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = '';
    }

    $scope.repo[attr][type] = $scope.goods[attr];

    return $scope.buildQueryDes();
  };

  /**
   * 產生查詢的文字描述
   */
  $scope.buildQueryDes = function () {
    var tailStr = '且';
    iterateRepo(tailStr);

    $scope.queryDes = $scope.queryDes.substring(0, $scope.queryDes.length - tailStr.length);
  };

  var iterateRepo = function (tailStr) {
    $scope.queryDes = '';

    for (var attr in $scope.repo) {
      if (!$scope.repo[attr]) {
        continue;
      }

      $scope.queryDes += $scope.mapping.attr[attr];

      iterateRepoAttr(attr, tailStr);
    };
  };

  var iterateRepoAttr = function (attr, tailStr) {
    for (var type in $scope.repo[attr]) {
      $scope.queryDes += $scope.mapping.type[type];

      if (!Array.isArray($scope.repo[attr][type])) {
        $scope.queryDes += $scope.repo[attr][type].name;
        $scope.queryDes += tailStr;

        continue;
      }

      iterateRepoType(attr, type);

      $scope.queryDes += tailStr;
    }
  };

  var iterateRepoType = function (attr, type) {
    for (var i = 0; i < $scope.repo[attr][type].length; i ++) {
      if (typeof $scope.repo[attr][type][i] === 'object') {
        $scope.queryDes += $scope.repo[attr][type][i].name + ((i === $scope.repo[attr][type].length - 1) ? '': '或');          
      } else {
        $scope.queryDes += $scope.repo[attr][type][i] + ((i === $scope.repo[attr][type].length - 1) ? '':'或');
      }
    }
  };

  var GoodsOperator = function () {
    this.selectChecked = function () {
      var tmp = [];

      for (var key in $scope.searchRepo) {
        if ($scope.searchRepo[key].isCheck) {
          tmp.push({id: $scope.searchRepo[key].id});
        }
      }

      return tmp;
    };

    this.onSaleChecked = function () {
      $http.put(Routing.generate('api_goodsPassport_onsale'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次上架完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刪除發生錯誤!');
        });
    };

    this.offSaleChecked = function () {
      $http.put(Routing.generate('api_goodsPassport_offsale'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次下架完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次下架發生錯誤！');
        });
    };
    
    this.deleteChecked = function () {
      $http.delete(Routing.generate('api_goodsPassport_reverse'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刪除完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刪除發生錯誤！');
        });
    };
    
    this.punchOutChecked = function () {
      $http.put(Routing.generate('api_activity_push', {id: $scope.punchActivity}), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刷出完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刷出發生錯誤！');
        });
    };
    
    this.punchInChecked = function () {
      $http.put(Routing.generate('api_activity_pull', {id: $scope.punchActivity}), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刷入完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刷入發生錯誤！');
        });
    };
  };

  $scope.initSearchMeta();
}]);
'use strict';

/* Controllers */

/**
 * 調貨後端流程:
 * 1. ApiBundle/OrderController 調貨的action, 產生兩筆訂單， 首先產生調出貨
 * 2. 調出貨觸發事件->產生調進貨訂單以及調進貨商品->prevent 商品監聽者產生進貨訂單
 *
 * 操作流程:
 * 1. 搜尋商品, 對該商品發出調貨請求，此時訂單狀態和商品狀態都沒有改變
 * 2. 調貨請求存入 MoveReq， 狀態為待處理, WebWorker 每隔三分鐘搜尋該實體一次並刷新通知
 * ( 
 * MoveRequest: {
 *  status: orderStatus, 
 *  req_store(發起請求店): store,
 *  res_store(接收請求店): store,
 *  res_user(回應請求人): user,
 *  req_user(請求發起人): user,
 *  goods_passport(商品): goods_passport,
 *  out_orders(調出貨訂單): orders,
 *  in_orders(掉進貨訂單): orders,
 *  create_at(建立時間): date,
 *  update_at(更新時間): date,
 *  memo: 備註(string)
 * })
 * 3a. 允諾該請求, 則啟動調貨後端流程，該請求狀態改為complete
 * 3b. 取消改請求，該請求取消，並附上原因
 *
 * 實作步驟:
 *
 * 1. Entity Move create, 關連設定和db schema 更新 v
 * 2. MoveRepository.php create 
 * 3. ApiBundle/MoveController 業務邏輯實作，基本CRUD
 * 4. ApiBundle/OrderController 調貨的業務邏輯實作
 * 5. GoodsSubscriber prevent 產生進貨訂單
 * 6. Angular 的 MoveCtrl 實作完成,內涵為查詢修改刪除介面( 新增應於商品查詢處實作v )
 * 7. Angular 的 HeaderCtrl 實作，內涵為請求以及寄賣完成的推播通知( 寄賣之後才會做 )
 */
backendCtrls.controller('MoveCtrl', [ '$scope', '$http', '$filter', 'Move', 'OrdersStatus', 'Store',
  function ($scope, $http, $filter, Move, OrdersStatus, Store) {

  var OS_HANDLING = 1;
  var GS_ON_SALE = 1;

  /**
   * 初始化資料
   */
  $scope.init = function () {
    $scope.success = false;
    $scope.error = false;
    $scope.queryDes = false;
    $scope.totalItems = 0;
    $scope.currentPage = 1;
    $scope.perPage = 10;

    // 取得目前使用者
    $http.get(Routing.generate('api_user_current')).success(function (user) {
      $scope.user = user;
    });

    $scope.statuses = OrdersStatus.query();
    $scope.stores = Store.query();
    $scope.queryDes = '';
    $scope.move = {};
    $scope.repo = {};
    // 取得 mapping 設定
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/mapping.json').
      success(function (res) {
        /**
         * 查詢描述英中對應陣列
         * @type {Object}
         */
        $scope.mapping = res;
      });
  };

  $scope.setSuccess = function (msg) {
    $scope.success = msg;
    $scope.error = false;
  };

  $scope.setError = function (msg) {
    $scope.success = false;
    $scope.error = msg;
  };

  /**
   * 判斷是否為本店商品
   * 
   * @param  {object}  move
   * @return {Boolean}     
   */
  $scope.isOwnGoods = function (move) {
    return ($scope.user.store.id === move.out_goods_passport.store.id);
  };

  /**
   * 判斷是否為本店發出的請求
   * 
   * @param  {object}  move
   * @return {Boolean}     
   */
  $scope.isOwnReq = function (move) {
    return ($scope.user.store.id === move.req_store.id);
  };

  /**
   * 判斷該請求商品是否為上架狀態
   * 
   * @param  {object}  move
   * @return {Boolean}     
   */
  $scope.isOnSale = function (move) {
    return (move.out_goods_passport.status.id === GS_ON_SALE);
  };

  /**
   * 判斷該調貨請求狀態是否為正在處理中
   * 
   * @param  {object}  move
   * @return {Boolean}     
   */
  $scope.isHandling = function (move) {
    return (move.status.id === OS_HANDLING);
  };

  /**
   * 查詢條件以及結果清空
   */
  $scope.reset = function () {
    $scope.repo = {};
    $scope.move = {};
    $scope.queryDes = false;
  };

  /**
   * 確認調出貨，此時
   * 
   * 1. 產生調出貨訂單和調進貨訂單，( 後端判斷商品狀態為上架且為本店商品才可執行以下動作 )，兩個訂單的狀態都為處理中
   * 2. 產品狀態改為調貨中
   * 3. 調貨請求綁訂調進貨以及調出貨訂單，回應者，備註更新
   * 4. 自動取消其他店對該商品的調貨請求
   *
   * @param  {object} move
   */
  $scope.send = function (move) {
    Move.update({id: move.id}, {act: 'send', memo: move.memo}, 
      function (newMove) {
      $scope.refresh($scope.searchRepo, move, newMove);
      $scope.setSuccess('出貨確認完成!');
    }, function (e) {
      console.log(e);
      $scope.setError('出貨確認失敗!');
    });
  };

  /**
   * 確認到貨
   *
   * 1. 調出貨訂單和調進貨訂單完成 ( 後端判斷商品狀態為調貨中且請求發起所屬店為本店 )
   * 2. 產品狀態改為它店
   * 3. 備註更新
   * 
   * @param  {object} move
   */
  $scope.recieve = function (move) {
    Move.update({id: move.id}, {act: 'recieve', memo: move.memo}, 
      function (newMove) {
      $scope.refresh($scope.searchRepo, move, newMove);
      $scope.setSuccess('到貨確認完成!');
    }, function (e) {
      console.log(e);
      $scope.setError('到貨確認失敗!');
    });
  };

  /**
   * 取消請求
   * 
   * 調進方:
   * 1. 調貨請求取消 ( 調貨請求狀態必須為處理中且為本店請求且商品狀態為上架才可取消 )
   * 2. 備註更新
   * 
   * 調出方:
   * 1. 若存在調出貨訂單和調進貨訂單，其狀態改為取消 ( 調貨請求狀態必須為處理中且商品為本店才可執行以下動作 )
   * 2. 產品狀態改為本店
   * 3. 備註更新
   * 
   * @param  {object} move
   */
  $scope.cancel = function (move) {
    Move.update({id: move.id}, {act: 'cancel', memo: move.memo}, 
      function (newMove) {
      $scope.refresh($scope.searchRepo, move, newMove);
      $scope.setSuccess('已取消!');
    }, function (e) {
      console.log(e);
      $scope.setError('取消失敗!');
    });
  };

  /**
   * 對該調貨請求處理完後，刷新其資料
   * 
   * @param  {object} move
   * @param  {object} elem
   * @param  {object} newElem
   */
  $scope.refresh = function (repo, elem, newElem) {
    var index = repo.indexOf(elem);

    if (index !== -1) {
      repo[index] = newElem;
    }
  };

  /**
   * 根據條件取得查詢結果
   */
  $scope.query = function () {
    $http.get(
      Routing.generate(
        "api_move_filter", 
        {
          jsonCondition: JSON.stringify($scope.repo), 
          page: $scope.currentPage, 
          perPage: $scope.perPage
        }
      )).
      success(function (moves) {
        for (var key in moves) {
          $scope.preventEntityError(moves[key]);
          $scope.formatDate(moves[key]);
          $scope.switchPanelAndRes();
        }

        $scope.searchRepo = moves;
      }).
      error(function (e) {
        console.log(e);
        $scope.setError('Woops! 查詢發生錯誤！');
      });
  };

  /**
   * 結果區域及輸入區域切換顯示
   */
  $scope.switchPanelAndRes = function () {
    $scope.isSearchPanelVisible = true;
    $scope.isSearchResVisible = false;
  };

  $scope.formatDate = function (move) {
    // 格式化時間
    move.create_at = $filter('date')(move.create_at, 'yyyy-MM-dd');
    move.update_at = $filter('date')(move.update_at, 'yyyy-MM-dd');
  };

  $scope.preventEntityError = function (model) {
    var r = ['brand', 'pattern', 'level', 'source', 'mt', 'supplier', 'color', 'req_store', 'res_store', 'in_goods_passport', 'out_goods_passport'];

    for (var i in r) {// 防止不存在實體引起錯誤
      if (typeof model[r[i]] === 'undefined') {
        model[r[i]] = {id: ''};
      }
    }
  };

  /**
   * 初始化頁籤
   */
  $scope.pageInit = function () {
    $http.get(Routing.generate("api_move_filter_count", {jsonCondition: JSON.stringify($scope.repo)})).
      success(function (total) {
        $scope.totalItems = total;
      });
  };

  /**
   * 換頁時觸發動作，這邊是執行 query() 方法取得資料
   */
  $scope.pageChanged = function() {
    $scope.query();
  };

  /**
   * 加入搜尋條件陣列
   * 
   * @param {string} attr [資料屬性]
   * @param {string type [邏輯類型{ notin, in, like ....}]
   */
  $scope.addInSearchRePo = function (attr, type) {
    if (!$scope.move[attr]) {
      return;
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = [];
    }

    $scope.repo[attr][type].push($scope.move[attr]);
    $scope.move[attr] = '';
    $scope.buildQueryDes();
  };

  /**
   * 隱藏/顯示 切換
   */
  $scope.switchDisplay = function (move) {
    move.isDisplay = !move.isDisplay;
  };

  /**
   * 設置相等查詢條件
   * 
   * @param {string} attr [屬性]
   * @param {string} type [邏輯類別]
   */
  $scope.setEqRepo = function (attr, type) {
    if (!$scope.move[attr]) {
      $scope.repo[attr] = false;

      return $scope.buildQueryDes();
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = '';
    }

    $scope.repo[attr][type] = $scope.move[attr];

    return $scope.buildQueryDes();
  };

  /**
   * 產生查詢的文字描述
   */
  $scope.buildQueryDes = function () {
    var tailStr = '且';
    iterateRepo(tailStr);

    $scope.queryDes = $scope.queryDes.substring(0, $scope.queryDes.length - tailStr.length);
  };

  var iterateRepo = function (tailStr) {
    $scope.queryDes = '';

    for (var attr in $scope.repo) {
      if (!$scope.repo[attr]) {
        continue;
      }

      $scope.queryDes += $scope.mapping.attr[attr];

      iterateRepoAttr(attr, tailStr);
    };
  };

  var iterateRepoAttr = function (attr, tailStr) {
    for (var type in $scope.repo[attr]) {
      $scope.queryDes += $scope.mapping.type[type];

      if (!Array.isArray($scope.repo[attr][type])) {
        $scope.queryDes += $scope.repo[attr][type].name;
        $scope.queryDes += tailStr;

        continue;
      }

      iterateRepoType(attr, type);

      $scope.queryDes += tailStr;
    }
  };

  var iterateRepoType = function (attr, type) {
    for (var i = 0; i < $scope.repo[attr][type].length; i ++) {
      if (typeof $scope.repo[attr][type][i] === 'object') {
        $scope.queryDes += $scope.repo[attr][type][i].name + ((i === $scope.repo[attr][type].length - 1) ? '': '或');          
      } else {
        $scope.queryDes += $scope.repo[attr][type][i] + ((i === $scope.repo[attr][type].length - 1) ? '':'或');
      }
    }
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('OrdersCtrl', ['$scope', '$routeParams', '$http', '$filter', 'OrdersStatus', 'OrdersKind', 'Brand', 'Supplier', 'Store', 'User', 'PayType', 'Activity',
  function ($scope, $routeParams, $http, $filter, OrdersStatus, OrdersKind, Brand, Supplier, Store, User, PayType, Activity) {
  /**
   * 初始化資料
   */
  $scope.init = function () {
    $scope.orders = {};
    $scope.repo = {
      Gname: {
        notIn: ['']
      }
    };
    $scope.orderBy = {attr: 'Oupdate_at'};
    $scope.orderDir = 'DESC';

    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.queryDes = false;
    $scope.totalItems = 0;
    $scope.currentPage = 1;
    $scope.perPage = 10;

    $scope.users = User.query();
    $scope.statuss = OrdersStatus.query();
    $scope.kinds = OrdersKind.query();
    $scope.brands = Brand.query();
    $scope.stores = Store.query();
    $scope.activitys = Activity.query();
    $scope.suppliers = Supplier.query();
    $scope.payTypes = PayType.query();
    $scope.orderBy = {attr: 'id', name: '索引'};
    $scope.orderDir = 'DESC';

    $scope.startAt = getToday();
    $scope.endAt = getToday();

    // 取得 mapping 設定
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/mapping.json').
      success(function (res) {
       /**
        * 查詢描述英中對應陣列
        * @type {Object}
        */
        $scope.mapping = res;

        $scope.query();
        $scope.pageInit();
      });

    // 取得排序資料
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/orders/order_conditions.json').
      success(function (res) {
        $scope.orderBys = res;
      });
  };

  /**
   * 有無成功或失敗訊息
   * @return {Boolean}
   */
  $scope.isMsg = function () {
    return ($scope.successMsg || $scope.errorMsg);
  };

  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 取消此訂單
   * @param {integer} index of $scope.ordersRepo
   */
  $scope.cancel = function (index) {
    var post = {
      cancel: true,
      memo: $scope.ordersRepo[index].memo
    };

    $http.delete(Routing.generate('api_orders_cancel', {id: $scope.ordersRepo[index].id}), post)
      .success(function (data) {
        // 若存在錯誤訊息則印出錯誤訊息，終止動作
        if (data.error) {
          return isError(data.error);
        }

        if (data.success) {
          // 刪除該筆訂單，回傳成功訊息表示為進貨類訂單，後端會把訂單和商品統統刪除
          $scope.ordersRepo.splice(index, 1);
          return isSuccess(data.success);
        }

        $http.get(Routing.generate('api_orders_show', {id : data}))
          .success(function (data) {
            $scope.ordersRepo[index] = data;
            isSuccess('訂單取消完成!');
          });
      }).error(function () {
        isError('訂單取消發生錯誤!');
      });
  };

  /**
   * 修改訂單資訊
   * @param {integer} index of $scope.ordersRepo
   */
  $scope.update = function (index) {
    var post = {
      paid: $scope.ordersRepo[index].paid,
      pay_type: $scope.ordersRepo[index].pay_type,
      memo: $scope.ordersRepo[index].memo,
      diff: $scope.ordersRepo[index].diff,
      content: $scope.ordersRepo[index].content
    };

    $http.put(Routing.generate('api_orders_update',{id: $scope.ordersRepo[index].id}), post)
      .success(function (id) {
        $http.get(Routing.generate('api_orders_show', {id : id}))
          .success(function (data) {
            $scope.ordersRepo[index] = data;
            $scope.ordersRepo[index].isDisplay = true;
            isSuccess('更新訂單成功');
          });
      }).error(function (e) {
        isError('更新訂單失敗');
      });
  };

  /**
   * 根據條件取得查詢結果
   */
  $scope.query = function () {
    $http.get(
      Routing.generate("api_orders_filter", 
      {
        jsonCondition: JSON.stringify($scope.repo), 
        page: $scope.currentPage, 
        perPage: $scope.perPage,
        jsonOrderBy: JSON.stringify({attr: $scope.orderBy.attr, dir: $scope.orderDir})
      })).
      success(function (ordersRepo) {
        if (ordersRepo.length === 0) {
          $scope.ordersRepo = ordersRepo;

          return isError('無符合條件訂單!');
        }

        for (var key in ordersRepo) {
          $scope.switchPanelAndRes();
        }

        $scope.ordersRepo = ordersRepo;

        isSuccess('查詢完成!');
      }).
      error(function (e) {
        console.log(e); 
        
        isError('Woops! 查詢發生錯誤！');
      });
  };

  /**
   * 結果區域及輸入區域切換顯示
   */
  $scope.switchPanelAndRes = function () {
    $scope.isSearchPanelVisible = true;
    $scope.isSearchResVisible = false;
  }    
 
  /**
   * 匯出檔案
   */
  $scope.export = function () {    
    window.location = Routing.generate("api_orders_export", {jsonCondition: JSON.stringify($scope.repo)});
  };

  /**
   * 初始化頁籤
   */
  $scope.pageInit = function () {
    $http.get(Routing.generate("api_orders_filter_count", {jsonCondition: JSON.stringify($scope.repo)})).
      success(function (total) {
        $scope.totalItems = total;
      });
  };

  /**
   * 換頁時觸發動作，這邊是執行 query() 方法取得資料
   */
  $scope.pageChanged = function() {
    $scope.query();
  };

  $scope.addTimeCondition = function (type) {
    var attr = type;

    $scope.orders[attr] = $scope.startAt;
    $scope.addInSearchRePo(type, 'gte');

    $scope.orders[attr] = $scope.endAt;
    $scope.addInSearchRePo(type, 'lte');
  };

  /**
   * 加入搜尋條件陣列
   * 
   * @param {string} attr [資料屬性]
   * @param {string type [邏輯類型{ notin, in, like ....}]
   */
  $scope.addInSearchRePo = function (attr, type) {
    if (!$scope.orders[attr]) {
      return;
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = [];
    }

    $scope.repo[attr][type].push($scope.orders[attr]);
    $scope.orders[attr] = '';

    $scope.buildQueryDes();
  };

  /**
   * 設置相等查詢條件
   * 
   * @param {string} attr [屬性]
   * @param {string} type [邏輯類別]
   */
  $scope.setEqRepo = function (attr, type) {
    if (!$scope.orders[attr]) {
      $scope.repo[attr] = false;
      $scope.buildQueryDes();

      return;
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = '';
    }

    $scope.repo[attr][type] = $scope.orders[attr];
    $scope.buildQueryDes();
  };

  /**
   * 查詢條件以及結果清空
   */
  $scope.reset = function () {
    $scope.repo = {};
    $scope.orders = {};
    $scope.queryDes = false;
  };

  /**
   * 隱藏/顯示 切換
   */
  $scope.switchDisplay = function (orders) {
    orders.isDisplay = !orders.isDisplay;
  };

  $scope.emptyMsg = function () {
    $scope.successMsg = null;
    $scope.errorMsg = null;
  };

  /**
   * 取得目前的時間，並且格式化為 yyyy-mm-dd
   * 
   * @return {string}
   */
  var getToday = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
  };

  /**
   * 產生查詢的文字描述
   */
  $scope.buildQueryDes = function () {
    var tailStr = '且';
    iterateRepo(tailStr);

    $scope.queryDes = $scope.queryDes.substring(0, $scope.queryDes.length - tailStr.length);
  };

  var iterateRepo = function (tailStr) {
    $scope.queryDes = '';

    for (var attr in $scope.repo) {
      if (!$scope.repo[attr]) {
        continue;
      }

      $scope.queryDes += $scope.mapping.attr[attr];

      iterateRepoAttr(attr, tailStr);
    };
  };

  var iterateRepoAttr = function (attr, tailStr) {
    for (var type in $scope.repo[attr]) {
      $scope.queryDes += $scope.mapping.type[type];

      if (!Array.isArray($scope.repo[attr][type])) {
        $scope.queryDes += $scope.repo[attr][type].name;
        $scope.queryDes += tailStr;

        continue;
      }

      iterateRepoType(attr, type);

      $scope.queryDes += tailStr;
    }
  };

  var iterateRepoType = function (attr, type) {
    for (var i = 0; i < $scope.repo[attr][type].length; i ++) {
      if (typeof $scope.repo[attr][type][i] === 'object') {
        $scope.queryDes += $scope.repo[attr][type][i].name + ((i === $scope.repo[attr][type].length - 1) ? '': '或');          
      } else {
        $scope.queryDes += $scope.repo[attr][type][i] + ((i === $scope.repo[attr][type].length - 1) ? '':'或');
      }
    }
  };

  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 修改成功函式
   */
  var isSuccess = function (msg) {
    $scope.successMsg = msg;
    $scope.errorMsg = false;
  };

  /**
   * 修改失敗函式
   */
  var isError = function (msg) {
    $scope.successMsg = false;
    $scope.errorMsg = msg;
  };

  var openPanel = function (index) {
    $scope.ordersRepo[index];
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

/**
 * 一般販售
 */
backendCtrls.controller('OrdersNormalCtrl', ['$scope', '$routeParams', '$http', '$filter', 'PayType',
  function ($scope, $routeParams, $http, $filter, PayType) { 
  /**
   * 商品狀態:活動
   * 
   * @type {integer}
   */
  var GS_ON_SALE = 1;

  /**
   * 不允許
   * 
   * @type {integer}
   */
  var NOT_ALLOWED = 0;

  /**
   * 售出訂單
   * 
   * @type {integer}
   */
  var OK_SOLDOUT = [6, 11, 13];

  /**
   * 付款方式: 刷卡
   * 
   * @type {integer}
   */
  var PT_CARD = 2;

  /**
   * 修改成功函式
   */
  var isSuccess = function (msg) {
    $scope.successMsg = msg;
    $scope.errorMsg = false;
  };

  /**
   * 修改失敗函式
   */
  var isError = function (msg) {
    $scope.successMsg = false;
    $scope.errorMsg = msg;
  };

  /**
   * 取得今天的日期
   * 
   * @return {string}
   */
  var getTodayDate = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();
  };

  /**
   * 初始化取得商品資料api 的 post 參數值，其內涵為商品查詢條件
   * 
   * @param  {object} barcode [刷入的產編條碼]
   * @return {object} post [會被送到server使用]
   */
  var getGoodsFilterPost = function (barcode) {
    /**
     * post 參數，用來取得結帳成功後的訂單資料
     * 
     * @type {object}
     */
    var post = {
      Gsn: {} // 產編
    };

    // 若存在條碼，則條件產編加入該條碼產編
    if (barcode) {
      post.Gsn.in = [barcode];
    }

    return post;
  };

  /**
   * 初始化訂單
   * 
   * @param  {object} goods
   * @return {object} orders
   */
  var getInitOrders = function (goods) {
    /**
     * 初始化訂單
     * 
     * @type {Object}
     */
    var orders = {
      pay_type: PT_CARD,// 預設使用刷卡
      custom: {
        email: '' // 客戶電子郵件信箱
      },
      required: goods.price, // 售價等於商品優惠價
      paid: goods.price // 已付金額預設為售價
    }

    return orders;
  };

  /**
   * 取得初始化後的發票物件
   * 
   * @param  {object} orders
   * @return {object} invoice
   */
  var getInitInvoice = function (orders) {
    var invoice = {};

    invoice.orders = {};
    invoice.sn = orders.invoice.sn;
    invoice.id = orders.invoice.id;
    invoice.create_at = orders.invoice.create_at;
    invoice.store = orders.invoice.store;
    invoice.user = orders.invoice.user;
    invoice.total = 0;

    return invoice;
  };

  /**
   * 初始化商品訂單
   * 
   * @param  {object} goods
   */
  var setGoodsOrders = function (goods) {
    // 預設折扣 1 (1 = 10/10)
    goods.discount = 10; 

    // 設置商品訂單屬性
    goods.orders = getInitOrders(goods);
  };

  /**
   * 設置手風琴的敘述
   *
   * @param {object} goods
   */
  var setHeading = function (goods) {
    goods.heading = '';
    goods.heading += goods.name + '  |  ' + goods.sn + '   |   ' + goods.brand.name;
    goods.heading += (goods.fake_price > 0) ? '   |   一般價:      ' + goods.fake_price + '元' : '';
    goods.heading += '   |    優惠價:' + goods.price + '元';
    goods.heading += (!goods.allow_discount) ? '    <不允許折扣>     ' : '';
  };

  /**
   * 設置原總金額
   */
  var setTotal = function () {
    $scope.total.org = 0;
    $scope.total.sale = 0;

    for (var key in $scope.goodsRepo) {
      $scope.total.org += $scope.goodsRepo[key].price;
      $scope.total.sale += $scope.goodsRepo[key].orders.required;
    }
  };

  /**
   * 設置 $scope.invoices 中的orders
   *
   * @param {object} orders
   */
  var setOrders = function (orders) {
    var invoice = $scope.invoices[orders.invoice.id.toString()];

    if (!invoice) {
      invoice = $scope.invoices[orders.invoice.id.toString()] = getInitInvoice(orders);
    }

    invoice.orders[orders.id.toString()] = orders;
  };

  /**
   * 成功結帳後的回呼函式
   * 
   * @param  {array} ordersIds [成功結帳的訂單id組成之陣列]
   */
  var successPayCallback = function (ordersIds) {
    // 如果成功結帳，則清空刷件容器
    $scope.goodsRepo = [];

    /**
     * 取得回傳訂單資料api的條件
     * 
     * @type {Object}
     */
    var condition = {
      Oid: {
        in: ordersIds
      }
    };

    /**
     * 將條件轉換成json字串已符合api 規定
     * 
     * @type {json}
     */
    var jCondition = JSON.stringify(condition);

    /**
     * 訂單api需要使用的參數，此為查詢條件的json字串
     * 
     * @type {Object}
     */
    var post = {
      jsonCondition: jCondition
    };
    
    $http.get(Routing.generate('api_orders_filter', post))
      .success(function (ordersGroup) {
        for (var key in ordersGroup) {
          setInvoices(ordersGroup[key]);
        }

        isSuccess('結帳完成，成功取得訂單資料!');
      })
      .error(function (e) {
        console.log(e);

        isError('結帳完成，但訂單資料顯示發生錯誤，請透過訂單查詢檢視詳細資料');
      });
  };

  /**
   * 訂單更新成功的回呼函式
   * 
   * @param  {integer} id [訂單的id]
   */
  var ordersUpdateSuccessCallbak = function (id) {
    $http.get(Routing.generate('api_orders_show', {id : id}))
      .success(function (orders) {
        // 預設為開啟
        orders.isDisplay = true;
        
        // 設置發票陣列中的訂單資訊
        setOrders(orders);
        
        isSuccess('更新訂單成功，已取得回傳資料!');
      })
      .error(function (e) {
        console.log(e);
        
        isError('更新訂單成功，但取得回傳資料時發生錯誤，請至訂單查詢檢視訂單詳細資料!')
      });
  };

  /**
   * 刪除訂單成功的回呼函式
   * 
   * @param  {object || integer} data [成功返回訂單id, 失敗則會存在data.error，亦即錯誤訊息]
   */
  var ordersDeleteSuccessCallback = function (data) {
    // 若存在錯誤訊息則印出錯誤訊息，終止動作
    if (data.error) {
      return isError(data.error);
    }

    var id = data;

    // 更新訂單資訊
    $http.get(Routing.generate('api_orders_show', {id : id}))
      .success(function (orders) {
        // 設置發票陣列中的訂單資訊
        setOrders(orders);
        
        isSuccess('訂單取消完成!');
      });
  };

  /**
   * 將訂單陣列重組成鍵值為訂單id 的陣列 
   * -> [{id: orders}]
   * 
   * @param  {object} orderses    [description]
   * @param  {object} ordersGroup [description]
   */
  var rebuildOrderses = function (orderses, ordersGroup) {
    for (var key in ordersGroup) {
      orderses[ordersGroup[key].id.toString()] = ordersGroup[key];
    }
  };

  /**
   * 設置 $scope.invoices 內涵
   *
   * @param {object} orders
   */
  var setInvoices = function (orders) {
    /**
     * 每張發票物件，其中orderses[key].invoice.id 為該次迭代訂單的所屬發票之id,
     * 用來當做發票陣列的鍵值
     * 
     * @type {object || boolean} boolean when non declared
     */
    var eachInvoice = $scope.invoices[orders.invoice.id.toString()];

    // 如果發票物件尚未宣告，
    // 則初始化其各屬性
    if (!eachInvoice) {
      $scope.invoices[orders.invoice.id.toString()] = eachInvoice = getInitInvoice(orders);
    }

    // 發票總金額
    eachInvoice.total += orders.required;

    // 若發票訂單屬性不存在，則宣告其為空物件
    if (!eachInvoice.orders[orders.id.toString()]) {
      eachInvoice.orders[orders.id.toString()] = {};
    }

    // 將訂單加入發票訂單屬性
    eachInvoice.orders[orders.id.toString()] = orders;
  };

  /**
   * 初始化今日一般銷貨記錄
   */
  var initTodayRecord = function () {
    /**
     * 訂單條件物件
     * 
     * @type {Object}
     */
    var condition = {
      Ocreate_at: {
        gte: {
          in: getTodayDate()
        }
      },
      Okind: {
        in: OK_SOLDOUT
      }
    };

    /**
     * 排序條件
     * 
     * @type {Object}
     */
    var orderBy = {
      attr: 'create_at',
      dir: 'DESC'
    };

    $http.get(Routing.generate('api_orders_filter', {
      jsonCondition: JSON.stringify(condition), 
      jsonOrderBy: JSON.stringify(orderBy)
    }))
    .success(function (ordersGroup) {
      /**
       * 訂單陣列
       * 
       * @type {Array}
       */
      var orderses = [];

      // 重組訂單陣列 [orderses]
      rebuildOrderses(orderses, ordersGroup);

      // 迭代每筆訂單已組成每個發票Group
      for (var key in orderses) {
        /**
         * 每筆訂單物件, 其中key 為其id
         * 
         * @type {object}
         */
        var eachOrders = orderses[key];
        
        // 設置訂單內涵
        setInvoices(eachOrders);
      }
    })
    .error(function (e) {
      console.log(e);

      isError('取得今日記錄時發生錯誤!');
    });
  };

  /**
   * 清空回饋訊息
   */
  $scope.emptyMsg = function () {
    isSuccess(null);
    isError(null);
  };

  /**
   * 初始化需要用到的參數，
   * 應該只會在剛開始時執行一次，
   * 之後整個controller 不該在有任何地方呼叫它
   */
  $scope.init = function () {
    // 發票
    $scope.invoices = {};

    // 宣告商品刷件容器
    $scope.goodsRepo = [];

    // 取得付費方式選項
    $scope.payTypes = PayType.query();

    // 初始化客戶物件
    $scope.custom = {
      allowNull: true
    };

    // 初始化總金額物件
    $scope.total = {
      org: 0,
      sale: 0
    };

    // 預設刷件銷貨區展開
    $scope.isBrushPanelVisible = true;

    // 排序依據
    $scope.prop = 'id';

    initTodayRecord();
  };

  /**
   * 格式化時間
   * 
   * @param  {string} date   
   * @param  {string} format [example: 'yyyy-mm-dd']
   * @return {string} [格式化以後的時間]
   */
  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd H:mm:ss';

    return $filter('date')(date, format);
  };

  /**
   * 根據刷入的條碼取得商品資料，
   * 商品資料若成功取得則初始化相關訂單資訊，
   * 將其加入刷件商品容器中。
   * 
   * @param  {string} barcode
   */
  $scope.get = function (barcode) {
    for (var key in $scope.goodsRepo) {
      if ($scope.goodsRepo[key].sn === barcode) {
        return;
      }
    }

    /**
     * 訂單查詢條件
     * 
     * @type {object}
     */
    var post = getGoodsFilterPost(barcode);

    var GS_ON_SALE = 1;

    $http.get(Routing.generate('api_goodsPassport_filter', {jsonCondition: JSON.stringify(post)}))
      .success(function (goodsGroup) {
        // 若商品資料為空，報錯終止後續動作
        if (goodsGroup.length === 0) {
          return isError(barcode + '資料無法取得，請確定該商品確實存在!'); 
        }

        if (parseInt(goodsGroup[0].status.id) !== GS_ON_SALE) {
          return isError(barcode + '為' + goodsGroup[0].status.name + '，非上架狀態!'); 
        }

        // 初始化此商品的訂單屬性
        setGoodsOrders(goodsGroup[0]);

        // 設置該商品的標題，品名，產編, 品牌，價格等文字敘述
        setHeading(goodsGroup[0]);
        
        // 將該商品加入商品刷件容器中
        $scope.goodsRepo.push(goodsGroup[0]);

        // 刷件欄位清空，方便連續刷件
        $scope.barcode = '';

        // 計算總金額
        setTotal();

        isSuccess(barcode + '資料取得成功!');
      })
      .error(function (e) {
        console.log('error');

        isError(barcode + '資料取得發生錯誤!');
      }); 
  };

  /**
   * 取消該筆刷件
   * 
   * @param  {object} goods [刷入的商品]
   */
  $scope.cancel = function (goods) {
    var index = $scope.goodsRepo.indexOf(goods);

    // 若商品確實存在商品刷件容器中，將其移除
    if (index !== -1) {
      $scope.goodsRepo.splice(index, 1);
    }

    // 計算總金額
    setTotal();
  };

  /**
   * 付款
   */
  $scope.pay = function () {
    /**
     * 特殊銷貨api需要使用的參數
     * 
     * @type {Object}
     */
    var payPost = {
      goods: $scope.goodsRepo, 
      custom: $scope.custom
    };

    // 結帳，與server溝通交換資料
    $http.post(Routing.generate('api_orders_normal'), payPost)
      .success(successPayCallback)
      .error(function (e) {
        console.log(e);

        isError('結帳失敗!');
      });
  };

  /**
   * 取消此訂單
   * 
   * @param {integer} index [index of $scope.ordersGroup]
   */
  $scope._cancel = function (orders) {
    // 刪除訂單(取銷售出)，與server溝通交換資料
    $http.delete(Routing.generate('api_orders_cancel', {id: orders.id}))
      .success(ordersDeleteSuccessCallback)
      .error(function (e) {
        console.log(e);

        isError('訂單取消發生錯誤!');
      });
  };

  /**
   * 修改訂單資訊
   * 
   * @param {integer} index [index of $scope.ordersGroup]
   */
  $scope.update = function (orders) {
    var post = {
      paid: orders.paid,
      pay_type: orders.pay_type,
      memo: orders.memo,
      diff: orders.diff, // 有一input欄位 name="diff", 其用途為方便記錄此次付款多少金額以利 ope 記錄
      content: orders.content
    };

    $http.put(Routing.generate('api_orders_update',{id: orders.id}), post)
      .success(ordersUpdateSuccessCallbak)
      .error(function (e) {
        console.log(e);

        isError('更新訂單失敗');
      });
  };

  /**
   * 透過email取得客戶資料，
   * 這邊關乎到訂單綁定的客戶
   * 
   * @param {string} email
   */
  $scope.getCustomByMail = function (email) {
    /**
     * 客戶查詢條件
     * 
     * @type {Object}
     */
    var con = {
      'Cemail': {
        'in': [email]
      }
    };

    $http.get(Routing.generate('api_custom_filter', {jsonCondition: JSON.stringify(con)})).
      success(function (res) {
        $scope.custom.id = res.id;
        $scope.custom.name = res.name + res.sex;
        $scope.custom.isExist = (res.length === 0) ? 0 : 1;

        isSuccess('取得客戶資料成功!');
      })
      .error(function (e) {
        console.log(e);

        isError('取得客戶資料發生錯誤!');
      });
  };

  /**
   * 清空客戶資料
   */
  $scope.setCustomToNull = function () {
    if ($scope.custom.allowNull) {
      $scope.custom.email = '';
      $scope.custom.name = '';
      $scope.custom.id = '';
    }
  };

  /**
   * 隱藏/顯示 切換
   */
  $scope.switchDisplay = function (orders) {
    orders.isDisplay = !orders.isDisplay;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

/**
 * 一般販售
 */
backendCtrls.controller('OrdersSpecialCtrl', ['$scope', '$routeParams', '$http', '$filter', 'PayType', 'Activity',
  function ($scope, $routeParams, $http, $filter, PayType, Activity) { 
  /**
   * 商品狀態:活動
   * 
   * @type {integer}
   */
  var GS_ACTIVITY = 6;

  /**
   * 不允許
   * 
   * @type {integer}
   */
  var NOT_ALLOWED = 0;

  /**
   * 售出訂單
   * 
   * @type {integer}
   */
  var OK_SPECIAL_SOLDOUT = 12;

  /**
   * 付款方式: 刷卡
   * 
   * @type {integer}
   */
  var PT_CARD = 2;

  /**
   * 訂單狀態: 取消
   */
  var OS_CANCEL = 3;

  /**
   * 修改成功函式
   */
  var isSuccess = function (msg) {
    $scope.successMsg = msg;
    $scope.errorMsg = false;
  };

  /**
   * 修改失敗函式
   */
  var isError = function (msg) {
    $scope.successMsg = false;
    $scope.errorMsg = msg;
  };

  /**
   * 活動處理訂單價格
   */
  var ActivityProcessor = function () {
    /**
     * 暫存資料初始設置，
     * 
     * 資料有:
     * 
     * 1.哪些商品可折扣
     * 2.其總售價為多少
     * 
     * @param {object} tmp
     *
     * {
     *   indexArr: [array], // 刷件容器中允許折扣商品的 id 組成之陣列
     *   allowToal: [integer], // 刷件容器中所有允許折扣的商品們的合計售價
     *   gift: [integer] // 刷件容器中所有商品計算後的滿額送金額
     * }
     */
    var setIndexAndAllowTotal = function (tmp) {
      // 迭代刷入商品容器
      for (var key in $scope.goodsRepo) {
        // 如果該商品允許折扣，
        // 則將其 id 加入 tmp.indexArr
        // 並且增加允許折扣總額
        if ($scope.goodsRepo[key].allow_discount && $scope.goodsRepo[key].price >= $scope.myActivity.exceed) {
          tmp.indexArr.push(key);
          tmp.allowTotal += parseInt($scope.goodsRepo[key].price);
        } else {
          $scope.goodsRepo[key].orders.paid = $scope.goodsRepo[key].price;
        }
      }
    };

    /**
     * 檢查是否有滿額贈活動
     * 
     * @return {Boolean}
     */
    var isHadExceedGift = function () {
      return ($scope.myActivity.exceed > 0 && $scope.myActivity.minus > 0);
    };

    /**
     * 設置滿額送總贈送金額，
     * 其邏輯為 滿一萬送一千的話，則滿兩萬送兩千，
     * 
     * Example:
     *
     * 小明買了一個15000的包包和一個17000的背包，兩個商品都允許折扣的話，則滿額送3000元，
     * 應付金額 = 15000 + 17000 - 3000 = 29000
     * 
     * @param {object} tmp [詳細格式請見line 111 ~ 118]
     */
    var setExceedGift = function (tmp) {
      // 優惠贈送總金額
      tmp.gift = parseInt($scope.myActivity.minus) * Math.floor(tmp.allowTotal / $scope.myActivity.exceed);
    };

    /**
     * 將贈送金額按照比例分給每個商品
     * 
     * @param  {object} tmp [詳細格式請見line 111 ~ 118]
     */
    var setAssignGift = function (tmp) {
      for (var key in tmp.indexArr) {
        /**
         * 此商品的分配贈送比例
         * 
         * @type {float}
         */
        var fraction = (parseInt($scope.goodsRepo[tmp.indexArr[key]].price) / parseInt(tmp.allowTotal));
        
        /**
         * 此商品的贈送配額
         * 
         * @type {integer}
         */
        var eachGift = Math.round(tmp.gift * fraction);

        /**
         * goods $index in $scope.goodsRepo
         * 
         * @type {object}
         */
        var index = tmp.indexArr[key];

        /**
         * 商品
         * 
         * @type {object}
         */
        var goods = $scope.goodsRepo[index];

        // 商品售價扣去贈送配額即為訂單應付金額
        goods.orders.required = parseInt(goods.price) - eachGift; 
        
        // 訂單已付金額預設為訂單售價
        goods.orders.paid = goods.orders.required;
      }
    };

    /**
     * 檢查活動是否有折扣
     * 
     * @return {Boolean}
     */
    var isHadDiscount = function () {
      return ($scope.myActivity.discount > 0 && $scope.myActivity.exceed > 0);
    };

    /**
     * 對允許折扣的商品進行折扣
     * 
     * @param {object} tmp [詳細格式請見line 111 ~ 118]
     */
    var setAssignDiscount = function (tmp) {
      for (var key in tmp.indexArr) {
        /**
         * goods $index in $scope.goodsRepo
         * 
         * @type {object}
         */
        var index = tmp.indexArr[key];

        /**
         * 商品
         * 
         * @type {object}
         */
        var goods = $scope.goodsRepo[index];

        // 訂單的售價設置為 (商品優惠價 * 活動折扣)
        goods.orders.required = Math.round(parseInt((goods.orders.required && goods.orders.required > 0) ? goods.orders.required : goods.price) * $scope.myActivity.discount);
        
        // 訂單的已付金額預設為訂單的售價
        goods.orders.paid = goods.orders.required;
      }
    };

    /**
     * 根據商品原始的優惠價設置訂單資訊
     */
    var setWithOrgPrice = function () {
      for (var key in $scope.goodsRepo) {
        /**
         * 商品
         * 
         * @type {object}
         */
        var goods = $scope.goodsRepo[key];

        // 訂單的售價設置為商品的優惠價
        goods.orders.required = goods.price;

        // 訂單的已付金額預設為訂單的售價
        goods.orders.paid = goods.price;
      }
    };

    /**
     * 按比例對商品進行折扣( 小數點四捨五入到整數位 )
     *
     * <大概作法>
     * 
     * 1. 先把可以折扣的商品挑出來, 挑出來意思為丟在一個tmp 陣列裡
     * 2. 算出總和，比對滿x送y 或是折扣規則( 折扣因為是單獨對應商品，比較沒有問題 )
     * 3. 求得 y 以後，按照tmp陣列的每個商品售價照比例分派
     * 4. orders.required = goods.price - y * fraction || goods.price * (activity.discount/10)
     * ps:
     *   1. 只允許一種折價方式，兩種都有則滿額贈優先!!!!
     *   2. 每次刷入商品都會自動將商品的已付改成應付金額，因此操作時請先把所有商品刷入再一一設定金額
     */
    this.setActivityProcessPrice = function () {
      /**
       * 暫存物件
       * 
       * @type {Object}
       */
      var tmp = {};

      /**
       * 可折扣商品於 $scope.goodsRepo 的索引值構成的陣列
       * 
       * @type {Array}
       */
      tmp.indexArr = [];

      /**
       * 可折扣商品的總售價
       * 
       * @type {Number}
       */
      tmp.allowTotal = 0;

      /**
       * 滿額贈送金額
       * 
       * @type {Number}
       */
      tmp.gift = 0;

      // 暫存資料初始設置
      setIndexAndAllowTotal(tmp);

      // 無允許折扣商品, 不進行計算
      if (tmp.indexArr.length === 0 || tmp.allowTotal === 0) {
        return setWithOrgPrice();
      }

      // 檢查有無滿額贈活動，若有則進行滿額送處理
      if (isHadExceedGift()) {
        setExceedGift(tmp);
        setAssignGift(tmp);

        return;
      }
      
      // 如果有折扣設定，則進行折扣運算處理
      if (isHadDiscount()) {
        return setAssignDiscount(tmp);
      }

      return false;
    };
  };

  /**
   * 取得今天的日期
   * 
   * @return {string}
   */
  var getTodayDate = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();
  };

  /**
   * 初始化取得商品資料api 的 post 參數值，其內涵為商品查詢條件
   * 
   * @param  {object} barcode [刷入的產編條碼]
   * @return {object} post [會被送到server使用]
   */
  var getGoodsFilterPost = function (barcode) {
    /**
     * post 參數，用來取得結帳成功後的訂單資料
     * 
     * @type {object}
     */
    var post = {
      Gsn: {}, // 產編
    };

    // 若存在條碼，則條件產編加入該條碼產編
    if (barcode) {
      post.Gsn.in = [barcode];
    }

    return post;
  };

  /**
   * 初始化訂單
   * 
   * @param  {object} goods
   * @return {object} orders
   */
  var getInitOrders = function (goods) {
    /**
     * 初始化訂單
     * 
     * @type {Object}
     */
    var orders = {
      pay_type: PT_CARD,// 預設使用刷卡
      custom: {
        email: '' // 客戶電子郵件信箱
      },
      required: goods.price, // 售價等於商品優惠價
      paid: goods.price // 已付金額預設為售價
    }

    return orders;
  };

  /**
   * 取得初始化後的發票物件
   * 
   * @param  {object} orders
   * @return {object} invoice
   */
  var getInitInvoice = function (orders) {
    var invoice = {};

    invoice.orders = {};
    invoice.sn = orders.invoice.sn;
    invoice.id = orders.invoice.id;
    invoice.create_at = orders.invoice.create_at;
    invoice.store = orders.invoice.store;
    invoice.user = orders.invoice.user;
    invoice.total = 0;

    return invoice;
  };

  /**
   * 初始化商品訂單
   * 
   * @param  {object} goods
   */
  var setGoodsOrders = function (goods) {
    // 預設折扣 1 (1 = 10/10)
    goods.discount = 10; 

    // 設置商品訂單屬性
    goods.orders = getInitOrders(goods);
  };

  /**
   * 設置手風琴的敘述
   *
   * @param {object} goods
   */
  $scope.setHeading = function (goods) {
    goods.heading = '';
    goods.heading += goods.name + '  |  ' + goods.sn + '   |   ' + goods.brand.name + '  |  ';
    goods.heading += '   成本:' + goods.cost + '元 ';
    goods.heading += (goods.fake_price > 0) ? '   |   一般價:      ' + goods.fake_price + '元' : '';
    goods.heading += '   |    優惠價(未含活動折扣):' + goods.price + '元';
    goods.heading += (!goods.allow_discount) ? '    <不允許折扣>     ' : '';
  };

  /**
   * 設置原總金額
   */
  var setTotal = function () {
    $scope.total.org = 0;
    $scope.total.sale = 0;

    for (var key in $scope.goodsRepo) {
      $scope.total.org += $scope.goodsRepo[key].org_price;
      $scope.total.sale += $scope.goodsRepo[key].orders.required;
    }
  };

  /**
   * 設置 $scope.invoices 中的orders
   *
   * @param {object} orders
   */
  var setOrders = function (orders) {
    var invoice = $scope.invoices[orders.invoice.id.toString()];

    if (!invoice) {
      invoice = $scope.invoices[orders.invoice.id.toString()] = getInitInvoice(orders);
    }

    invoice.orders[orders.id.toString()] = orders;
  };

  /**
   * 成功結帳後的回呼函式
   * 
   * @param  {array} ordersIds [成功結帳的訂單id組成之陣列]
   */
  var successPayCallback = function (ordersIds) {
    // 如果成功結帳，則清空刷件容器
    $scope.goodsRepo = [];

    /**
     * 取得回傳訂單資料api的條件
     * 
     * @type {Object}
     */
    var condition = {
      Oid: {
        in: ordersIds
      }
    };

    /**
     * 將條件轉換成json字串已符合api 規定
     * 
     * @type {json}
     */
    var jCondition = JSON.stringify(condition);

    /**
     * 訂單api需要使用的參數，此為查詢條件的json字串
     * 
     * @type {Object}
     */
    var post = {
      jsonCondition: jCondition
    };
    
    $http.get(Routing.generate('api_orders_filter', post))
      .success(function (ordersGroup) {
        for (var key in ordersGroup) {
          setInvoices(ordersGroup[key]);
        }

        isSuccess('結帳完成，成功取得訂單資料!');
      })
      .error(function (e) {
        console.log(e);

        isError('結帳完成，但訂單資料顯示發生錯誤，請透過訂單查詢檢視詳細資料');
      });
  };

  /**
   * 訂單更新成功的回呼函式
   * 
   * @param  {integer} id [訂單的id]
   */
  var ordersUpdateSuccessCallbak = function (id) {
    $http.get(Routing.generate('api_orders_show', {id : id}))
      .success(function (orders) {
        // 預設為開啟
        orders.isDisplay = true;
        
        // 設置發票陣列中的訂單資訊
        setOrders(orders);
        
        isSuccess('更新訂單成功，已取得回傳資料!');
      })
      .error(function (e) {
        console.log(e);
        
        isError('更新訂單成功，但取得回傳資料時發生錯誤，請至訂單查詢檢視訂單詳細資料!')
      });
  };

  /**
   * 刪除訂單成功的回呼函式
   * 
   * @param  {object || integer} data [成功返回訂單id, 失敗則會存在data.error，亦即錯誤訊息]
   */
  var ordersDeleteSuccessCallback = function (data) {
    // 若存在錯誤訊息則印出錯誤訊息，終止動作
    if (data.error) {
      return isError(data.error);
    }

    var id = data;

    // 更新訂單資訊
    $http.get(Routing.generate('api_orders_show', {id : id}))
      .success(function (orders) {
        // 設置發票陣列中的訂單資訊
        setOrders(orders);
        
        isSuccess('訂單取消完成!');
      });
  };

  

  /**
   * 將訂單陣列重組成鍵值為訂單id 的陣列 
   * -> [{id: orders}]
   * 
   * @param  {object} orderses    [description]
   * @param  {object} ordersGroup [description]
   */
  var rebuildOrderses = function (orderses, ordersGroup) {
    for (var key in ordersGroup) {
      orderses[ordersGroup[key].id.toString()] = ordersGroup[key];
    }
  };

  /**
   * 設置 $scope.invoices 內涵
   *
   * @param {object} orders
   */
  var setInvoices = function (orders) {
    /**
     * 每張發票物件，其中orderses[key].invoice.id 為該次迭代訂單的所屬發票之id,
     * 用來當做發票陣列的鍵值
     * 
     * @type {object || boolean} boolean when non declared
     */
    var eachInvoice = $scope.invoices[(999999 - orders.invoice.id).toString()];

    // 如果發票物件尚未宣告，
    // 則初始化其各屬性
    if (!eachInvoice) {
      $scope.invoices[(999999 - orders.invoice.id).toString()] = eachInvoice = getInitInvoice(orders);
    }

    // 發票總金額
    eachInvoice.total += orders.required;

    // 若發票訂單屬性不存在，則宣告其為空物件
    if (!eachInvoice.orders[orders.id.toString()]) {
      eachInvoice.orders[orders.id.toString()] = {};
    }

    // 將訂單加入發票訂單屬性
    eachInvoice.orders[orders.id.toString()] = orders;
  };

  /**
   * 初始化今日活動銷貨記錄
   */
  var initThisActivityRecord = function () {
    // 取得活動銷貨記錄
    var condition = {
      // Ocreate_at: {
      //   gte: {
      //     in: getTodayDate()
      //   }
      // },
      Gactivity: {
        in: [$scope.myActivity]
      },
      Okind: {
        in: [OK_SPECIAL_SOLDOUT]
      }
    };

    /**
     * 排序條件
     * 
     * @type {Object}
     */
    var orderBy = {
      attr: 'invoice',
      dir: 'DESC'
    };

    $http.get(Routing.generate('api_orders_filter', {
      jsonCondition: JSON.stringify(condition), 
      jsonOrderBy: JSON.stringify(orderBy)
    }))
    .success(function (ordersGroup) {
      /**
       * 訂單陣列
       * 
       * @type {Array}
       */
      var orderses = [];

      // 重組訂單陣列 [orderses]
      rebuildOrderses(orderses, ordersGroup);

      // 迭代每筆訂單已組成每個發票Group
      for (var key in orderses) {
        /**
         * 每筆訂單物件, 其中key 為其id
         * 
         * @type {object}
         */
        var eachOrders = orderses[key];

        var department = $scope.departments[eachOrders.goods_passport.store.id.toString()];

        if (!department) {
          department = $scope.departments[eachOrders.goods_passport.store.id.toString()] = {
            required: 0,
            amount: 0,
            name: eachOrders.goods_passport.store.name
          };
        }

        if (eachOrders.status.id !== OS_CANCEL) {
          department.required += parseInt(eachOrders.required);

          department.amount += 1; 
        }
        
        // 設置訂單內涵
        setInvoices(eachOrders);
      }
    })
    .error(function (e) {
      console.log(e);

      isError('取得今日記錄時發生錯誤!');
    });
  };

  $scope.isEmpty = function (obj) {
    return Object.keys(obj).length === 0;
  };    

  $scope.countObj = function (obj) {
    return Object.keys(obj).length;
  }

  $scope.getDepartmentsTotal = function (type) {
    var sum = 0;

    for (var key in $scope.departments) {
      sum += $scope.departments[key][type];
    }

    return sum;
  };

  /**
   * 清空回饋訊息
   */
  $scope.emptyMsg = function () {
    isSuccess(null);
    isError(null);
  };

  $scope.changeActivity = function () {
    initThisActivityRecord();
  };

  /**
   * 初始化需要用到的參數，
   * 應該只會在剛開始時執行一次，
   * 之後整個controller 不該在有任何地方呼叫它
   */
  $scope.init = function () {
    // 發票
    $scope.invoices = {};

    // 部門群
    $scope.departments = {};

    // 取得活動選項
    Activity.query(function (res) {
      $scope.activitys = res;
      $scope.myActivity = $scope.activitys[0];

      if ($routeParams.id > 0) {
        for (var key in $scope.activitys) {
          if (parseInt($scope.activitys[key].id) === parseInt($routeParams.id)) {
            $scope.myActivity = $scope.activitys[key];

            break;
          }
        }
      }

      initThisActivityRecord();
    });

    // 宣告商品刷件容器
    $scope.goodsRepo = [];

    // 取得付費方式選項
    $scope.payTypes = PayType.query();

    // 初始化客戶物件
    $scope.custom = {
      allowNull: true
    };

    // 初始化總金額物件
    $scope.total = {
      org: 0,
      sale: 0
    };

    // 預設刷件銷貨區展開
    $scope.isBrushPanelVisible = true;

    // 排序依據
    $scope.prop = 'id';

    $scope.ActivityProcessor = new ActivityProcessor;

    $scope.todayTotalGrade = 0;
  };

  /**
   * 格式化時間
   * 
   * @param  {string} date   
   * @param  {string} format [example: 'yyyy-mm-dd']
   * @return {string} [格式化以後的時間]
   */
  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';

    return $filter('date')(date, format);
  };

  /**
   * 根據刷入的條碼取得商品資料，
   * 商品資料若成功取得則初始化相關訂單資訊，
   * 將其加入刷件商品容器中。
   * 
   * @param  {string} barcode
   */
  $scope.get = function (barcode) {
    for (var key in $scope.goodsRepo) {
      if ($scope.goodsRepo[key].sn === barcode) {
        return;
      }
    }

    /**
     * 訂單查詢條件
     * 
     * @type {object}
     */
    var post = getGoodsFilterPost(barcode);

    var GS_ACTIVITY = 6;

    $http.get(Routing.generate('api_goodsPassport_filter', {jsonCondition: JSON.stringify(post)}))
      .success(function (goodsGroup) {
        // 若商品資料為空，報錯終止後續動作
        if (goodsGroup.length === 0) {
          return isError(barcode + '資料無法取得，請確定該商品確實存在!'); 
        }

        if (parseInt(goodsGroup[0].status.id) !== GS_ACTIVITY) {
          return isError(barcode + '狀態為' + goodsGroup[0].status.name + ', 活動銷貨僅允許活動狀態商品!')
        }

        if (!goodsGroup[0].activity || parseInt(goodsGroup[0].activity.id) !== parseInt($scope.myActivity.id)) {
          return isError(barcode +  '為' + goodsGroup[0].activity.name  + '之商品，非' + $scope.myActivity.name + '之商品!');
        }

        // 初始化此商品的訂單屬性
        setGoodsOrders(goodsGroup[0]);

        // 設置該商品的標題，品名，產編, 品牌，價格等文字敘述
        $scope.setHeading(goodsGroup[0]);

        goodsGroup[0].org_price = goodsGroup[0].price;
        
        // 將該商品加入商品刷件容器中
        $scope.goodsRepo.push(goodsGroup[0]);

        // 刷件欄位清空，方便連續刷件
        $scope.barcode = '';

        // 根據活動設置商品訂單的金額
        $scope.ActivityProcessor.setActivityProcessPrice();

        // 計算總金額
        setTotal();

        isSuccess(barcode + '資料取得成功!');
      })
      .error(function (e) {
        console.log('error');

        isError(barcode + '資料取得發生錯誤!');
      }); 
  };

  /**
   * 取消該筆刷件
   * 
   * @param  {object} goods [刷入的商品]
   */
  $scope.cancel = function (goods) {
    var index = $scope.goodsRepo.indexOf(goods);

    // 若商品確實存在商品刷件容器中，將其移除
    if (index !== -1) {
      $scope.goodsRepo.splice(index, 1);
    }

    // 根據活動設置商品訂單的金額
    $scope.ActivityProcessor.setActivityProcessPrice();

    // 計算總金額
    setTotal();
  };

  /**
   * 付款
   */
  $scope.pay = function () {
    /**
     * 特殊銷貨api需要使用的參數
     * 
     * @type {Object}
     */
    var payPost = {
      goods: $scope.goodsRepo, 
      custom: $scope.custom
    };

    // 結帳，與server溝通交換資料
    $http.post(Routing.generate('api_orders_special'), payPost)
      .success(successPayCallback)
      .error(function (e) {
        console.log(e);

        isError('結帳失敗!');
      });
  };

  /**
   * 取消此訂單
   * 
   * @param {integer} index [index of $scope.ordersGroup]
   */
  $scope._cancel = function (orders) {
    // 刪除訂單(取銷售出)，與server溝通交換資料
    $http.delete(Routing.generate('api_orders_cancel', {id: orders.id}))
      .success(ordersDeleteSuccessCallback)
      .error(function (e) {
        console.log(e);

        isError('訂單取消發生錯誤!');
      });
  };

  /**
   * 修改訂單資訊
   * 
   * @param {integer} index [index of $scope.ordersGroup]
   */
  $scope.update = function (orders) {
    var post = {
      paid: orders.paid,
      pay_type: orders.pay_type,
      memo: orders.memo,
      diff: orders.diff, // 有一input欄位 name="diff", 其用途為方便記錄此次付款多少金額以利 ope 記錄
      content: orders.content
    };

    $http.put(Routing.generate('api_orders_update',{id: orders.id}), post)
      .success(ordersUpdateSuccessCallbak)
      .error(function (e) {
        console.log(e);

        isError('更新訂單失敗');
      });
  };

  /**
   * 透過email取得客戶資料，
   * 這邊關乎到訂單綁定的客戶
   * 
   * @param {string} email
   */
  $scope.getCustomByMail = function (email) {
    /**
     * 客戶查詢條件
     * 
     * @type {Object}
     */
    var con = {
      'Cemail': {
        'in': [email]
      }
    };

    $http.get(Routing.generate('api_custom_filter', {jsonCondition: JSON.stringify(con)})).
      success(function (res) {
        $scope.custom.id = res.id;
        $scope.custom.name = res.name + res.sex;
        $scope.custom.isExist = (res.length === 0) ? 0 : 1;

        isSuccess('取得客戶資料成功!');
      })
      .error(function (e) {
        console.log(e);

        isError('取得客戶資料發生錯誤!');
      });
  };

  /**
   * 清空客戶資料
   */
  $scope.setCustomToNull = function () {
    if ($scope.custom.allowNull) {
      $scope.custom.email = '';
      $scope.custom.name = '';
      $scope.custom.id = '';
    }
  };

  /**
   * 隱藏/顯示 切換
   */
  $scope.switchDisplay = function (orders) {
    orders.isDisplay = !orders.isDisplay;
  };

  /**
   * 檢查活動是否有折扣
   * 
   * @param  {object}  activity
   * @return {Boolean}         
   */
  $scope.isDiscount = function (activity) {
    return (typeof activity !== 'undefined' && activity.discount > 0);
  };

  /**
   * 檢查活動是否有滿額贈活動
   * 
   * @param  {object}  activity 
   * @return {Boolean}
   */
  $scope.isGiftWithPurchase = function (activity) {
    return (typeof activity !== 'undefined' && activity.exceed > 0 &&  activity.minus > 0);
  };

  /**
   * 計算目前總價及優惠金額
   */
  $scope.countActivitySale = function () {
    // 根據活動設置商品訂單的金額
    $scope.ActivityProcessor.setActivityProcessPrice();

    // 計算總金額
    setTotal();
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('PatternCtrl', ['$scope', '$routeParams', '$http', 'Pattern',
	function ($scope, $routeParams, $http, Pattern) { 

  $scope.init = function () {
    $scope.patterns = Pattern.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
  	Pattern.save(query).
	  	$promise.then(function () {
	  		$scope.successMsg = query.name + ' 新增完成!';
        $scope.errorMsg = null;
	  		$scope.patterns = Pattern.query();
	  		$scope.query.name = '';
	  	}, function (error) {
        $scope.successMsg = null;
	  		$scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
	  	});
  };

  $scope.update = function (pattern) {

  	if (pattern.name === $scope.tmp.name) {
  		return;
  	}

  	Pattern.update({ id: pattern.id}, pattern).
  		$promise.then(function () {
  			$scope.successMsg = pattern.name + ' 修改完成!';
  			//$scope.patterns = Pattern.query();
  			$scope.query.name = '';
  		}, function () {
  			$scope.errorMsg = pattern.name + '修改失敗，請確認是否有名稱重複!';
  		});
  };

  $scope.destroy = function (pattern) {
  	Pattern.delete({ id: pattern.id}).
  		$promise.then(function () {
  			$scope.successMsg = pattern.name + ' 刪除完成!';
  			$scope.patterns = Pattern.query();
  			$scope.query.name = '';
  		}, function (e) {
  			$scope.errorMsg = pattern.name + '刪除失敗，請確認是否有綁定資料!';
  		});
  };

  $scope.clean = function () {
  	$scope.successMsg = false;
  	$scope.errorMsg = false;
  };

  $scope.setTmp = function (pattern) {
  	$scope.tmp.id = pattern.id;
  	$scope.tmp.name = pattern.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('PayTypeCtrl', [ function () {}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsImportCtrl', ['$scope', '$routeParams', '$http', '$timeout', '$upload', '$filter',
  function ($scope, $routeParams, $http, $timeout, $upload, $filter) {
  /**
   * 檔案選擇時，馬上顯示圖片
   */
  $scope.onFileSelect = function ($files, type) {
    $scope[type] = $files;
    
    // 取出第0個元素
    var $file = $files[0];
    
    // 這段不是很懂... 總之就是將上傳的圖片透過 HTML5 的 API 立即顯示
    if (window.FileReader && $file.type.indexOf('image') > -1) {
      var fileReader = new FileReader();
      
      fileReader.readAsDataURL($files[0]);
      
      var loadFile = function(fileReader) {
        fileReader.onload = function(e) {
          $timeout(function() {
            if (goods) {
              goods.imgpath = e.target.result;
            }
          });
        }
      }(fileReader);
    }
  };

  /**
   * 批次上傳商品(匯入 excel )
   */
  $scope.import = function () {
    if (!$scope.importfiles) {
      return;
    }

    for (var i = 0; i < $scope.importfiles.length; i++) {
      var file = $scope.importfiles[i];

      $scope.upload = $upload.upload({
        url: Routing.generate('api_goodsPassport_import'),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj}, // 這行坦白說我不知道意思@@
        file: file, 
      }).success(function(data, status, headers, config) {
        if (data.error) {
          var msg = data.error;

          $scope.errorEntity = {};
          $scope.errorEntity.resource = data.resource;
          $scope.errorEntity.name = data.name;
          
          return $scope.isError(msg);
        }

        $scope.errorEntity = false;

        for (var key in data) {
          // 防止空實體造成錯誤
          $scope.preventEntityError(data[key]);

          // 將取得的資料放入scope
          $scope.importGoods.push(data[key]);
        }
        // 清除要上傳的檔案
        $scope.cleanFile('#import-file');

        $scope.isSuccess('批次上傳成功');
      }).error(function (e) {
        $scope.errorEntity = false;
        $scope.isError('批次上傳失敗');
      });
    }
  };

  $scope.addEntity = function () {
    $http.post(Routing.generate('api_' + $scope.errorEntity.resource + '_create'), {name: $scope.errorEntity.name})
      .success(function (res) {
        $scope.isSuccess(res.name + '新增完成!');
        $scope.errorEntity = false;
      })
      .error(function () {
        $scope.isError($scope.errorEntity.name + '新增失敗!');
      });
  };
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsKeyInCtrl', ['$scope', '$routeParams', '$http', '$timeout', '$upload', '$filter',
  function ($scope, $routeParams, $http, $timeout, $upload, $filter) { 
  /**
   * 取得目前的時間，並且格式化為 yyyy-mm-dd
   * 
   * @return {string}
   */
  var getToday = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
  };

  $scope.formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 將圖片檔案透過 ajax 傳送到 server 儲存
   */
  $scope.uploadMulti = function ($files, ids, goodses) {
    if (!$files) {
      return;
    }

    //$files: an array of files selected, each file has name, size, and type.
    for (var i = 0; i < $files.length; i++) {
      var file = $files[i];

      $scope.upload = $upload.upload({
        url: Routing.generate('img_upload_multi', {ids: JSON.stringify(ids)}),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj},
        file: file, 
      }).success(function (data, status, headers, config) {
        for (var key in goodses) {
          goodses[key].imgpath = data;
        }

        // 加入成功訊息
        $scope.isSuccess('新增成功');
      }).error(function (e) {
        $scope.isError('圖片上傳失敗');
      });
    }
  };

  // 一般新增
  $scope.save = function ($files) {    
    $http.post(Routing.generate("api_goodsPassport_create"), $scope.goods).
      success(function (goods) {    
        if (goods.error) {
          return $scope.isError(goods.error);
        }

        // 上傳圖片會用到
        var ids = [];

        // 迴圈處理時間格式資料
        for (var key in goods) {
          // 防止空實體造成錯誤
          $scope.preventEntityError(goods[key]);

          // 加入新增成功的陣列
          $scope.addGoods.push(goods[key]);

          // 加入ids
          ids.push(goods[key].id);
        }

        // 上傳圖片
        $scope.uploadMulti($files, ids, $scope.addGoods);

        $scope.isSuccess('新增商品完成!');
      })
      .error(function (e) {
        $scope.isError('新增失敗!');
      });
  };

  // 手動上傳完成後將表單相關的變數還原
  $scope.initKeyInForm = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.goods = {};
    $scope.goods.amount = 1;
    $scope.goods.imgpath = $scope.img404;
    $scope.goods.allow_discount = 1;
    $scope.goods.in_type = 0;
    $scope.goods.is_web = 1;
    $scope.goods.purchase_at = getToday();
    $scope.goods.paid = 0;
    $scope.goods.feedback = 0;
    $scope.goods.pattern = {id: ''};
    $scope.goods.source = {id: ''};
    $scope.goods.brand = {id: ''};
    $scope.goods.level = {id: ''};
    $scope.goods.mt = {id: ''};
    $scope.goods.supplier = {id: 1};
    $scope.goods.color = {id: ''};

    $http.get(Routing.generate('api_user_current'))
      .success(function (user) {
        $scope.goods.store = user.store;
      })
      .error(function (e) {
        console.log(e);

        $scope.goods.store = {};
      });
  };

  $scope.initKeyInForm();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('GoodsPassportCtrl', [ '$scope', '$http', '$filter', '$timeout', '$upload', '$sce', '$modal', '$log', 'Brand', 'Pattern', 'Color', 'GoodsLevel', 'GoodsSource', 'Supplier', 'PayType', 'GoodsMT', 'GoodsStatus', 'Store', 'Move', 'Activity',
 function ($scope, $http, $filter, $timeout, $upload, $sce, $modal, $log, Brand, Pattern, Color, GoodsLevel, GoodsSource, Supplier, PayType, GoodsMT, GoodsStatus, Store, Move, Activity) { 
  
  $scope.initMeta = function() {
    $scope.entity = {};
    $scope.entity.brands = Brand.query();
    $scope.entity.patterns = Pattern.query();
    $scope.entity.colors = Color.query();
    $scope.entity.suppliers = Supplier.query();
    $scope.entity.mts = GoodsMT.query();
    $scope.entity.activitys = Activity.query();
    $scope.entity.goodsSources = GoodsSource.query();
    $scope.entity.goodsLevels = GoodsLevel.query();
    $scope.entity.stores = Store.query();
    $scope.entity.goodsStatuss = GoodsStatus.query();
    $scope.entity.isWebRadios = [{text: '是', value: 1}, {text: '否', value: 0}];
    $scope.entity.isAllowDiscountRadios = [{text: '是', value: 1}, {text: '否', value: 0}];
    $scope.entity.isInTypeRadios = [{text: '一般', value: 0}, {text: '寄賣', value: 1}];
    $scope.img404 = '/img/404.png';
    $scope.importGoods = []; // 批次上傳後回傳的商品資料
    $scope.addGoods = [];// 單筆上傳後回傳的商品資料( 回傳為陣列特別注意!!! )
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.globalOrders = []; // 訂單modal要用的變數
  };

  $scope.goSell = function () {
    window.location.href='#/normal';
  };

  $scope._formatDate = function (date, format) {
    var format = format || 'yyyy-MM-dd';
    return $filter('date')(date, format);
  };

  /**
   * 檔案選擇時，馬上顯示圖片
   */
  $scope.onFileSelect = function ($files, type, goods) {
    if (goods) {
      goods[type] = $files;
    } else {
      $scope[type] = $files;
    }
    
    // 取出第0個元素
    var $file = $files[0];
    
    // 這段不是很懂... 總之就是將上傳的圖片透過 HTML5 的 API 立即顯示
    if (window.FileReader && $file.type.indexOf('image') > -1) {
      var fileReader = new FileReader();
      
      fileReader.readAsDataURL($files[0]);
      
      var loadFile = function(fileReader) {
        fileReader.onload = function(e) {
          $timeout(function() {
            if (goods) {
              goods.imgpath = e.target.result;
            }
          });
        }
      }(fileReader);
    }
  };

  /**
   * Lazy load 圖片(展開商品資料時才讀取)
   */
  $scope.setLazyImg = function (goods) {
    goods.imgpathLazy = goods.imgpath;
  };

  /**
   * 移除圖片
   */
  $scope.removeImg = function (goods) {
    goods.imgpath = $scope.img404;
    $scope.setLazyImg(goods);
  };

  $scope.isSuccess = function (msg, goods) {
    $scope.successMsg = (goods) ?  goods.name + '(' + goods.sn + ')' + msg : '' + msg;
    $scope.successMsg = $sce.trustAsHtml($scope.successMsg);
    $scope.errorMsg = false;
  };

  $scope.isError = function (msg, goods) {
    $scope.errorMsg = (goods) ? goods.name + '(' + goods.sn + ')' + msg : '' + msg;
    $scope.errorMsg = $sce.trustAsHtml($scope.errorMsg);
    $scope.successMsg = false;
  };

  $scope.emptyMsg = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  }

  // 清除上傳的圖片或檔案
  $scope.cleanFile = function (selector) {
    $(selector).val('');// 很無奈的只能先用jQuery處理了，angualr 對file 的支援好少喔
  };

  $scope.delete = function (goods) {
    $http.delete(Routing.generate('api_goodsPassport_delete', {id: goods.id}), goods).
      success(function (res) {
      goods.isDelete = true;
      $scope.isSuccess('刪除完成', goods);
    });
  };

  $scope.splice = function (repo, elem) {
    var index = repo.indexOf(elem);

    if (index !== -1) {
      repo.splice(index, 1);
    }
  };

  /**
   * 還原上傳操作
   */
  $scope.reverse = function (model, msg) {
    var postId = [];

    for (var key in $scope[model]) {
      postId.push($scope[model][key].id);
    }

    // 這段api 要小心用，其實就是把商品不留痕跡的抹除，這東西太危險目前限制只在還原時使用，平常商品刪除就只能通過destroy 或下架
    $http.delete(Routing.generate('api_goodsPassport_reverse', {jsonIds: JSON.stringify(postId)}))
      .success(function () {      
        $scope[model] = [];
        $scope.isSuccess(msg + '還原完成!');
      }).error(function () {
        $scope.isError(msg + '還原失敗!');
      });
  };

  $scope.put = function (index, repo) {
    var goods = repo[index];

    $http.put(Routing.generate("api_goodsPassport_update", {id: goods.id}), goods).
      success(function (returnGoods) {
        returnGoods.isSuccess = true;

        repo[index] = returnGoods;
        
        $scope.setLazyImg(goods);
        
        $scope.isSuccess('修改成功', goods);
      })
      .error(function (e) {
        $scope.isError('修改失敗', goods);
      });
  }

  /**
   * 更新商品資訊
   * @param  {integer} index 
   * @param  {array}
   */
  $scope.update = function (index, repo) {
    var goods = repo[index];
    var $files = goods.files;

    if (typeof $files === 'undefined' || !$files) {
      return $scope.put(index, repo);
    }

    for (var i = 0; i < $files.length; i++) {
      var file = $files[i];
      $scope.upload = $upload.upload({
        url: Routing.generate('img_upload', {id: goods.id}),
        method: 'POST',
        withCredentials: true,
        data: { myObj: $scope.myModelObj},
        file: file, 
      }).progress(function(evt) { // 可以監聽上傳過程，不過這邊我們用不到就是
        //$scope.progress = parseInt(100.0 * evt.loaded / evt.total);
      }).success(function(data, status, headers, config) {
        goods.imgpath = data;
        $scope.put(index, repo);
      }).error(function (e) {
        $scope.isError('圖片修改失敗');
      });
    }
  };

  $scope.move = function (goods) {
    Move.save({id: goods.id}, function (res) {
      if (res.msg) {
        return $scope.isError(res.msg);
      }

      $scope.isSuccess('訂貨請求成功發送!');
    });
  };

  $scope.preventEntityError = function (model) {
    var r = ['brand', 'pattern', 'level', 'source', 'mt', 'supplier', 'color'];

    for (var i in r) {// 防止不存在實體引起錯誤
      if (typeof model[r[i]] === 'undefined') {
        model[r[i]] = {id: ''};
      }
    }
  };

  $scope.open = function (goods) {
    var post = {Gsn: {in: [goods.sn]}};

    $http.get(Routing.generate('api_orders_filter', {jsonCondition: JSON.stringify(post)}))
      .success(function (orderses) {
        var modalInstance = $modal.open({
          templateUrl: 'myModalContent.html',
          controller: ModalInstanceCtrl,
          size: false,
          resolve: {
            orderses: function () {
              return $scope.globalOrders = orderses;
            }
          }
        });

        $scope.isSuccess(false);
      }).error(function () {
        $scope.isError('訂單讀取發生錯誤!');
      });
  };

  $scope.initMeta();
}]);
'use strict';

/* Controllers */

/**
 * 商品搜尋及相關CRUD
 */
backendCtrls.controller('GoodsSearchCtrl', ['$scope', '$routeParams', '$http', '$upload', '$timeout', '$filter',
  function ($scope, $routeParams, $http, $upload, $timeout, $filter) { 
  
  var GoodsOperator;

  /**
   * 取得目前的時間，並且格式化為 yyyy-mm-dd
   * 
   * @return {string}
   */
  var getToday = function () {
    var today = new Date();

    return today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
  };
  
  /**
   * 初始化資料
   */
  $scope.initSearchMeta = function () {
    $scope.goods = {};
    $scope.repo = {};
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.queryDes = false;
    $scope.orderDir = 'ASC';
    $scope.totalItems = 0;
    $scope.currentPage = 1;
    $scope.perPage = 10;
    $scope.orderBy = {attr: 'id', name: '索引'};
    $scope.orderDir = 'DESC';
    $scope.searchRepo = [];
    $scope.actType = '';
    $scope.checkStatus = true;
    $scope.punchActivity = {
      description: "借出",
      discount: 0,
      end_at: "2016-04-09T00:00:00+0800",
      exceed: 0,
      id: 1,
      minus: 0,
      name: "借出",
      start_at: "2014-08-01T00:00:00+0800",
    };

    $scope.startAt = getToday();
    $scope.endAt = getToday();

    $scope.isAllowEdit = true;

    // 取得排序資料
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/goods/order_conditions.json').
      success(function (res) {
        $scope.orderBys = res;
      });

    // 取得 mapping 設定
    $http.get('/bundles/woojinbackend/js/angular-seed/app/js/mapping.json').
      success(function (res) {
        /**
         * 查詢描述英中對應陣列
         * @type {Object}
         */
        $scope.mapping = res;
      });

    GoodsOperator = new GoodsOperator;
  };

  $scope.addTimeCondition = function (type) {
    var attr = type;

    $scope.goods[attr] = $scope.startAt;
    $scope.addInSearchRePo(type, 'gte');

    $scope.goods[attr] = $scope.endAt;
    $scope.addInSearchRePo(type, 'lte');
  };

  $scope.checkAll = function () {
    for (var key in $scope.searchRepo) {
      $scope.searchRepo[key].isCheck = $scope.checkStatus;
    }
  };
  
  $scope.doAct = function () {
    GoodsOperator[$scope.actType]();
  };
 
  /**
   * 根據條件取得查詢結果
   */
  $scope.query = function () {
    $http.get(
      Routing.generate(
        "api_goodsPassport_filter", 
        {
          jsonCondition: JSON.stringify($scope.repo), 
          page: $scope.currentPage, 
          perPage: $scope.perPage,
          jsonOrderBy: JSON.stringify({attr: $scope.orderBy.attr, dir: $scope.orderDir})
        }
      )).
      success(function (goods) {
        for (var key in goods) {
          $scope.preventEntityError(goods[key]);

          $scope.switchPanelAndRes();
        }

        if (goods.length === 0) {
          $scope.switchPanelAndRes();
          $scope.isError('查無商品!');
        }

        $scope.searchRepo = goods;
      }).
      error(function () {
        $scope.isError('Woops! 查詢發生錯誤！');
      });
  };

  /**
   * 結果區域及輸入區域切換顯示
   */
  $scope.switchPanelAndRes = function () {
    $scope.isSearchPanelVisible = true;
    $scope.isSearchResVisible = false;
  };

  /**
   * 匯出檔案
   */
  $scope.export = function () {    
    window.location = Routing.generate("api_goodsPassport_export", {jsonCondition: JSON.stringify($scope.repo)});
  };

  /**
   * 查詢條件以及結果清空
   */
  $scope.reset = function () {
    $scope.repo = {};
    $scope.goods = {};
    $scope.queryDes = false;
  };

  /**
   * 初始化頁籤
   */
  $scope.pageInit = function () {
    $http.get(Routing.generate("api_goodsPassport_filter_count", {jsonCondition: JSON.stringify($scope.repo)})).
      success(function (total) {
        $scope.totalItems = total;
      });
  };

  /**
   * 換頁時觸發動作，這邊是執行 query() 方法取得資料
   */
  $scope.pageChanged = function() {
    $scope.query();
  };

  /**
   * 加入搜尋條件陣列
   * 
   * @param {string} attr [資料屬性]
   * @param {string type [邏輯類型{ notin, in, like ....}]
   */
  $scope.addInSearchRePo = function (attr, type) {
    if (!$scope.goods[attr]) {
      return;
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = [];
    }

    $scope.repo[attr][type].push($scope.goods[attr]);
    $scope.goods[attr] = '';
    $scope.buildQueryDes();
  };

  /**
   * 設置相等查詢條件
   * 
   * @param {string} attr [屬性]
   * @param {string} type [邏輯類別]
   */
  $scope.setEqRepo = function (attr, type) {
    if (!$scope.goods[attr]) {
      $scope.repo[attr] = false;

      return $scope.buildQueryDes();
    }

    if (!$scope.repo[attr]) {
      $scope.repo[attr] = {};
    }

    if (!$scope.repo[attr][type]) {
      $scope.repo[attr][type] = '';
    }

    $scope.repo[attr][type] = $scope.goods[attr];

    return $scope.buildQueryDes();
  };

  /**
   * 產生查詢的文字描述
   */
  $scope.buildQueryDes = function () {
    var tailStr = '且';
    iterateRepo(tailStr);

    $scope.queryDes = $scope.queryDes.substring(0, $scope.queryDes.length - tailStr.length);
  };

  var iterateRepo = function (tailStr) {
    $scope.queryDes = '';

    for (var attr in $scope.repo) {
      if (!$scope.repo[attr]) {
        continue;
      }

      $scope.queryDes += $scope.mapping.attr[attr];

      iterateRepoAttr(attr, tailStr);
    };
  };

  var iterateRepoAttr = function (attr, tailStr) {
    for (var type in $scope.repo[attr]) {
      $scope.queryDes += $scope.mapping.type[type];

      if (!Array.isArray($scope.repo[attr][type])) {
        $scope.queryDes += $scope.repo[attr][type].name;
        $scope.queryDes += tailStr;

        continue;
      }

      iterateRepoType(attr, type);

      $scope.queryDes += tailStr;
    }
  };

  var iterateRepoType = function (attr, type) {
    for (var i = 0; i < $scope.repo[attr][type].length; i ++) {
      if (typeof $scope.repo[attr][type][i] === 'object') {
        $scope.queryDes += $scope.repo[attr][type][i].name + ((i === $scope.repo[attr][type].length - 1) ? '': '或');          
      } else {
        $scope.queryDes += $scope.repo[attr][type][i] + ((i === $scope.repo[attr][type].length - 1) ? '':'或');
      }
    }
  };

  var GoodsOperator = function () {
    this.selectChecked = function () {
      var tmp = [];

      for (var key in $scope.searchRepo) {
        if ($scope.searchRepo[key].isCheck) {
          tmp.push({id: $scope.searchRepo[key].id});
        }
      }

      return tmp;
    };

    this.onSaleChecked = function () {
      $http.put(Routing.generate('api_goodsPassport_onsale'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次上架完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刪除發生錯誤!');
        });
    };

    this.offSaleChecked = function () {
      $http.put(Routing.generate('api_goodsPassport_offsale'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次下架完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次下架發生錯誤！');
        });
    };
    
    this.deleteChecked = function () {
      $http.delete(Routing.generate('api_goodsPassport_reverse'), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刪除完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刪除發生錯誤！');
        });
    };
    
    this.punchOutChecked = function () {
      $http.put(Routing.generate('api_activity_push', {id: $scope.punchActivity}), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刷出完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刷出發生錯誤！');
        });
    };
    
    this.punchInChecked = function () {
      $http.put(Routing.generate('api_activity_pull', {id: $scope.punchActivity}), {goodsPost: this.selectChecked()})
        .success(function (res) {
          $scope.isSuccess('批次刷入完成！');

          $scope.query();
        })
        .error(function (e) {
          console.log(e);

          $scope.isError('批次刷入發生錯誤！');
        });
    };
  };

  $scope.initSearchMeta();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('SupplierCtrl', ['$scope', '$routeParams', '$http', 'Supplier',
  function ($scope, $routeParams, $http, Supplier) { 

  $scope.init = function () {
    $scope.suppliers = Supplier.query();
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.query = {};
    $scope.query.name = '';
  };

  $scope.create = function (query) {
    Supplier.save(query).
      $promise.then(function () {
        $scope.successMsg = query.name + ' 新增完成!';
        $scope.errorMsg = null;
        $scope.suppliers = Supplier.query();
        $scope.query.name = '';
      }, function (error) {
        $scope.successMsg = null;
        $scope.errorMsg = query.name + '新增失敗，請確認是否有名稱重複!';
      });
  };

  $scope.update = function (supplier) {

    if (supplier.name === $scope.tmp.name) {
      return;
    }

    Supplier.update({ id: supplier.id}, supplier).
      $promise.then(function () {
        $scope.successMsg = supplier.name + ' 修改完成!';
        //$scope.suppliers = Supplier.query();
        $scope.query.name = '';
      }, function () {
        $scope.errorMsg = supplier.name + '修改失敗，請確認是否有名稱重複!';
      });
  };

  $scope.destroy = function (supplier) {
    Supplier.delete({ id: supplier.id}).
      $promise.then(function () {
        $scope.successMsg = supplier.name + ' 刪除完成!';
        $scope.suppliers = Supplier.query();
        $scope.query.name = '';
      }, function (e) {
        $scope.errorMsg = supplier.name + '刪除失敗，請確認是否有綁定資料!';
      });
  };

  $scope.clean = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  };

  $scope.setTmp = function (supplier) {
    $scope.tmp.id = supplier.id;
    $scope.tmp.name = supplier.name;
  };

  $scope.init();
}]);
'use strict';

/* Controllers */

backendCtrls.controller('UserCtrl', ['$scope', '$routeParams', '$http', 'User', 'Store', 'Role',
  function ($scope, $routeParams, $http, User, Store, Role) { 

  var isSuccess = function (user) {
    $scope.successMsg = '';
    $scope.successMsg += user.username + ' 新增完成';
    $scope.errorMsg = false;
  };

  var isFailed = function () {
    $scope.errorMsg = ($scope.user.username) ? $scope.user.username + '新增失敗' : '新增失敗';
    $scope.successMsg = false;
  };

  $scope.initData = function () {
    $scope.query = {};
    $scope.query.role = {};
    $scope.query.store = {};
    $scope.tmp = {}; // 檢查資料有無改動，依此結果判斷是否要和後端溝通
    $scope.users = User.query();
    $scope.query.is_active = true;
    $scope.query.store.id = 1;
    $scope.query.role.id = 3;
    $scope.successMsg = false;
    $scope.errorMsg = false;
    $scope.stores = Store.query();
    $scope.roles = Role.query();
  };

  $scope.save = function () {   
    
    $scope.clean();

    $http.post(Routing.generate("api_user_create"), $scope.query).
      success(function (user) {
        $scope.initData();
        isSuccess(user);
      })
      .error(function (e) {
        isFailed();
        console.log(e);
      });
  };

  $scope.update = function (user) {
    if (
      ($scope.tmp.id === user.id) &&
      ($scope.tmp.realname === user.realname) &&
      ($scope.tmp.username === user.username) &&
      ($scope.tmp.email === user.email) &&
      ($scope.tmp.mobil === user.mobil) &&
      ($scope.tmp.address === user.address) &&
      ($scope.tmp.is_active === user.is_active) &&
      ($scope.tmp.store === user.store) 
    ) {
      return;
    }

    $scope.clean();

    User.update({ id: user.id}, user).
      $promise.then(function () {
        $scope.successMsg = user.username + ' 修改完成!';
        //$scope.users = User.query();
      }, function (e) {
        $scope.errorMsg = user.username + '修改失敗，請確認是否有名稱重複!';
        console.log(e);
      });
  };

  $scope.destroy = function (user) {
    
    $scope.clean();

    User.delete({ id: user.id}).
      $promise.then(function () {
        $scope.successMsg = user.username + ' 刪除完成!';
        $scope.users = User.query();
      }, function (e) {
        $scope.errorMsg = user.username + '刪除失敗，請確認是否有綁定資料!';
        console.log(e);
      });
  };

  $scope.clean = function () {
    $scope.successMsg = false;
    $scope.errorMsg = false;
  };

  $scope.setTmp = function (user) {
    $scope.tmp.id = user.id;
    $scope.tmp.realname = user.realname;
    $scope.tmp.username = user.username;
    $scope.tmp.email = user.email;
    $scope.tmp.mobil = user.mobil;
    $scope.tmp.address = user.address;
    $scope.tmp.is_active = user.is_active;
    $scope.tmp.store = user.store;
  };

  $scope.initData();
}]);
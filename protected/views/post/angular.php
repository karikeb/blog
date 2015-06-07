<div ng-app="abmPost" ng-controller="postController">
    <form>
        <table>
        <tr><td>ID</td><td><input ng-model="postEdit.id" type='text'></td></tr>
        <tr><td>TITULO</td><td><input ng-model="postEdit.title" type='text'></td></tr>
        <tr><td>CONTENIDO</td><td><input ng-model="postEdit.content" type='text'></td></tr>
        <tr><td>SATUS</td><td><input ng-model="postEdit.status" type='text'></td></tr>
        <tr><td><button ng-click="guardar()">GUARDAR</button></td></tr>
        <tr><td><button ng-click="crear()">CREAR</button></td></tr>
        </table>
    </form>
    <table>
        <tr ng-repeat="postActual in listaPost">
            <td>{{postActual.id}}</td>
            <td>{{postActual.title}}</td>
            <td><button ng-click="editar(postActual)">Editar</button></td>
            <td><button ng-click="borrar(postActual)">BORRAR</button></td>
        </tr>
    </table>
</div>
<script>
var app = angular.module('abmPost',['ngResource']);
app.factory('postFactory', function($http, $resource){
    return $resource('/blog/index.php/api/posts/:id', null, {
    update: {
      method: 'PUT' // this method issues a PUT request
    }
  });
});

app.factory('sessionInjector',  function() {  
    var sessionInjector = {
        request: function(config) {
            config.headers['x-username'] = 'demo';
            config.headers['x-password'] = 'demo';
            return config;
        }
    };
    return sessionInjector;
});
app.config(['$httpProvider', function($httpProvider) {  
    $httpProvider.interceptors.push('sessionInjector');
}]);
app.config(['$resourceProvider', function($resourceProvider) {
  // Don't strip trailing slashes from calculated URLs
  $resourceProvider.defaults.stripTrailingSlashes = false;
}]);

app.controller('postController', function(postFactory, $scope){
   // $http.get('/blog/index.php/api/posts').then(function(respuesta){
   //     $scope.listaPost = respuesta.data;
   // });
   
    $scope.postEdit = {};
    
    postFactory.query(function(respuesta){
        $scope.listaPost = respuesta;
    });
    
    $scope.crear = function(){
        $scope.postEdit.id = null;
        postFactory.save($scope.postEdit,function(response){
            $scope.listaPost.push(response)
        });
    }
    
    $scope.editar = function(postActual){
        angular.copy(postActual, $scope.postEdit);
        
    }
    
    $scope.guardar = function(){
        postFactory.update({id:$scope.postEdit.id},$scope.postEdit);
    }
    
    $scope.borrar = function(postActual){
        postFactory.delete({id:postActual.id}, function(response){
            $scope.listaPost = $scope.listaPost.filter(function(item){
               return item.id !== postActual.id; 
            });
        }
        );
    }
    
    
});

</script>
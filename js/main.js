var app = angular.module('todo-list', ['ngRoute', 'ngResource']);
var apiHTML = 'https://featherbrained-exec.000webhostapp.com/todo_list/api/index.php'

app.factory('list', function($http) { 
	var service = {};

	service.save = function(entry) {
		console.log(entry);
		
		if ( entry.id != undefined ) {
			var salida = "";
			if ( service.entries[entry.id].id == entry.id ) {
					salida = service.entries[entry.id];
			}
			if ( salida != '' ) {
				if ( entry.texto != service.entries[entry.id].texto ) {
					console.log(service.entries[entry.id].texto);
					console.log(entry.texto);
					console.log('actualizar');
					var datos = JSON.stringify({
	                	texto: entry.texto,
	                	estado: entry.estado
	            	});
					$http.post(apiHTML + '/update/' + entry.id, datos).success(function(data, status) {
		    		console.log(data);
		            if ( data != 0 ) {
		            	service.entries = data;
					}
	        		})
				}
			} 
		} else {
			var datos = JSON.stringify({
	        	texto: entry.texto
	        });
	        $http.post(apiHTML + '/new', datos).success(function(data, status) {
	        	console.log(data);
	        	if ( data != 0 ) {
	        		service.entries = data;
	        	}
	        }) 	
		}
	}

	service.delete = function(id) {
		if ( id == 'All' || id == 'Active' || id == 'Completed') {
			$http.get(apiHTML + '/delete/'+id).	
			success(function(data){
				console.log(data);
				console.log(JSON.parse(data));
				if ( data == -1 ) {
					service.entries = [];
				} else if ( data != 0 ) {
					service.entries = JSON.parse(data);
				}
  			})
			.error(function(data, status){
				alert('error all!');
			});
		} else {			
			console.log(apiHTML + '/delete/'+id);
			$http.get(apiHTML + '/delete/'+id).	
			success(function(data){
				//service.get();			
				console.log(JSON.parse(data));
				if ( data == -1 ) {
					service.entries = [];
				} else if ( data != 0 ) {
					service.entries = JSON.parse(data);
				}
  			})
			.error(function(data, status){
			});
		}  	
	}

	service.toggle = function(id) {
		console.log(service.entries[id]);
		console.log(service.entries[id].estado);
		if ( service.entries[id].estado == 0 ) {			
			nuevo_estado = 1;					
		} else {
			nuevo_estado = 0;					
		}

    	var datos = JSON.stringify({
                texto: service.entries[id].texto,
                estado: nuevo_estado
            });

    	$http.post(apiHTML + '/update/' + id, datos).success(function(data, status) {
    		console.log(data);
            if ( data != 0 ) {
            	service.entries = data;
			}
        })
	}

	service.entries = [];

	$http.get(apiHTML). // + '/index.php').	
		success(function(data){
			console.log(data);
			service.entries = data;      
	  	})
		.error(function(data, status){
			alert('error!');
		});	

	return service;
});


app.config(['$routeProvider', function($routeProvider){
	$routeProvider  
	.when('/listar/:estado', {
		templateUrl: 'view/inicio.html',
		controller: 'listar'
	})
	.when('/form', {
		templateUrl: 'view/form.html',
		controller: 'crear'
	})    
	.when('/form/edit/:id', {
		templateUrl: 'view/form.html',
		controller: 'crear'
	})    
	.when('/delete/:id', {
		templateUrl: 'view/borrar.html',
		controller: 'borrar'
	})
	.otherwise({
		redirectTo: '/listar/All'
	});
}]);


app.controller('listar', ['$scope', '$routeParams', '$location', 'list', function($scope, $routeParams, $location, list) {		
	var salida = [];
	$scope.clase_subrayado = '';
	$scope.clase_all = 'btn-info';
	$scope.clase_active = '';
	$scope.clase_completed = '';
	$scope.borrar = "All";
	if ( $routeParams.estado != 'All' ) {
		if ( $routeParams.estado == 0 ) {
			$scope.clase_all = '';
			$scope.clase_active = 'btn-info';
			$scope.borrar = "Active";
		} else if ( $routeParams.estado == 1 ) {
			$scope.clase_all = '';
			$scope.clase_completed = 'btn-info';
			$scope.borrar = "Completed";
		}
		
		for (x in list.entries) {
    		if ( list.entries[x].estado == $routeParams.estado ) {
				salida.push(list.entries[x]);
			}
		}
	} else {
		salida = list.entries;	
	}
	
	$scope.lista = salida;

	$scope.toggle = function(id) {		
		list.toggle(id);
	}
	
	$scope.$watch(function () { return list.entries; }, function (entries) {
		var salida = [];
		if ( $routeParams.estado == 0 ) {
			$scope.clase_all = '';
			$scope.clase_active = 'btn-info';
			$scope.borrar = "Active";
		} else if ( $routeParams.estado == 1 ) {
			$scope.clase_all = '';
			$scope.clase_completed = 'btn-info';
			$scope.borrar = "Completed";
		}			

		for (x in entries) {
	    	if ( $routeParams.estado == 0 || $routeParams.estado == 1 ) {
	    		if ( entries[x].estado == $routeParams.estado ) {
	    			salida.push(entries[x]);
	    		} 
	    	} else {
	    		salida.push(entries[x]);
	    	}
	    }
		$scope.lista = salida;
	});
}]);



app.controller('borrar', ['$scope', '$routeParams', '$location', 'list', function($scope, $routeParams, $location, list){		
	if ( $routeParams.id == 'All' || $routeParams.id == 'Active' || $routeParams.id == 'Completed' ) {
		$scope.texto = $routeParams.id;
	} else {
		$scope.texto = list.entries[$routeParams.id].texto;
	}

	$scope.remove = function(){
		console.log($routeParams.id);
		list.delete($routeParams.id);
		$location.path('/');
	}
}]);

app.controller('crear', ['$scope', '$routeParams', '$location', 'list', function($scope, $routeParams, $location, list) {
	$scope.texto = '';


	if(!$routeParams.id) {
		$scope.nuevo = {texto: '', estado: 0};		
	} else {
		var texto_act = list.entries[$routeParams.id].texto;
		var estado_act = list.entries[$routeParams.id].estado;
		$scope.nuevo = {id: $routeParams.id, texto: texto_act, estado: estado_act};
	}

	$scope.save = function() {
		list.save($scope.nuevo);
		$location.path('/');
	}
}]);

app.directive('tlTask', function(){
	return {
		restrict: 'E',
		templateUrl: 'view/task.html'
	};	
});



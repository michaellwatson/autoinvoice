var chartData = [{
	key: "Cumulative Return",
	values: [{
		"label": "A",
		"value": "-29.76464645"
	}, {
		"label": "B",
		"value": "29.76464645"
	}, {
		"label": "C",
		"value": "10.9549854"
	}, {
		"label": "D",
		"value": "18.484593485"
	}, {
		"label": "E",
		"value": "-98.9685965"
	}, {
		"label": "F",
		"value": "20"
	}, {
		"label": "G",
		"value": "-20"
	}, {
		"label": "H",
		"value": "10"
	}]
}];

var getValues = function () {
	var labs = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
	var ourData = [];
	
	for (var i = 0; i < labs.length; ++i) {
		ourData.push({
			label: labs[i],
			value: Math.floor(Math.random() = 100) - 50
		})
	}
	
	consloe.log('ourData:', ourData);
}
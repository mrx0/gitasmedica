

//
	
var tooth_status_arr = {
	1: {
			"descr": "Отсутствует",
			"color": "#F0F0F0",
			"img": "1.png"
		},
	2: {
			"descr": "Удален",
			"color": "#6F6F6F",
			"img": "3.png"
		},
	3: {
			"descr": "Имплантант",
			"color": "#FFF",
			"img": "4.png"
		},
	4: {
			"descr": "Формирователь десны",
			"color": "#FFA4FF",
			"img": "5.png"
		},
	5: {
			"descr": "Коронка",
			"color": "#FFFF00",
			"img": "6.png"
		},
	6: {
			"descr": "Коронка (имплант)",
			"color": "#FFFF00",
			"img": "6.png"
		},
	7: {
			"descr": "Коронка (вкладка)",
			"color": "#008200",
			"img": "7.png"
		},
	8: {
			"descr": "Бюгельный протез",
			"color": "#700000",
			"img": "48.png"
		},
	9: {
			"descr": "Мост",
			"color": "#D0B077",
			"img": "9.png"
		},
	10: {
			"descr": "Искусственный зуб",
			"color": "#FFF",
			"img": "10.png"
		},
	14: {
			"descr": "Полный съемный протез",
			"color": "#0000FF",
			"img": "14.png"
		},
	15: {
			"descr": "Частичный съемный",
			"color": "#01FFFF",
			"img": "15.png"
		},
	18: {
			"descr": "Сдвиг зубов",
			"color": "#000",
			"img": "40.png"
		},
	19: {
			"descr": "Молочный",
			"color": "#D595FD",
			"img": "2.png"
		},
	20: {
			"descr": "Ретенция",
			"color": "#EB7333",
			"img": "41.png"
		},
	//21: {
	//		"descr": "Полуретенция",
	//		"color": "#6F579F",
	//		"img": "45.png",
	//	},
	22: {
			"descr": "З.О.",
			"color": "#FF0000",
			"img": "46.png"
		},
	23: {
			"descr": "Шинирование",
			"color": "#FFF",
			"img": "../surface_state/38.png"
		},
	24: {
			"descr": "Подвижность",
			"color": "#FFF",
			"img": "../surface_state/38.png"
		},
	25: {
			"descr": "Ретейнер",
			"color": "#FFF",
			"img": "../surface_state/38.png"
		},
	26: {
			"descr": "Сверхкомплект",
			"color": "#FFF",
			"img": "../surface_state/38.png"
		},
	27: {
			"descr": "Чужой протез",
			"color": "#00FF00",
			"img": "8.png"
		}
};
	
	
var tooth_alien_status_arr = {
	11: {
			"descr": "Чужая коронка",
			"color": "#FF9900",
			"img": "11.png"
		},
	12: {
			"descr": "Чужой мост",
			"color": "#AD4700",
			"img": "12.png"
		},
	13: {
			"descr": "Чужой бюгель",
			"color": "#8F570F",
			"img": "13.png"
		},
	16: {
			"descr": "Чужой полный съемный протез",
			"color": "#BF27DF",
			"img": "16.png"
		},
	17: {
			"descr": "Чужой частичный съемный",
			"color": "#8FBFBF",
			"img": "17.png"
		}
};
	
var surfaces_arr = ["status", "pin", "alien", "surface1", "surface2", "surface3", "surface4", "top1", "top2", "top12", "root1", "root2", "root3"];


var root_status_arr = {
		31: {
			"descr": "Здоровый",
			"color": "#FFF",
			"img": "18.png"
		},
		32: {
			"descr": "Удален",
			"color": "#000",
			"img": "19.png"
		},
		33: {
			"descr": "Частич. пломб.",
			"color": "#00FFFF",
			"img": "20.png"
		},
		34: {
			"descr": "Корень (радикс)",
			"color": "#FF0000",
			"img": "21.png"
		},
		35: {
			"descr": "Штифт",
			"color": "#FFFF00",
			"img": "22.png"
		},
		36: {
			"descr": "Инородное тело",
			"color": "#003F87",
			"img": "23.png"
		},
		37: {
			"descr": "Перфорация",
			"color": "#CCC",
			"img": "24.png"
		},
		38: {
			"descr": "Пломбировка",
			"color": "#0000FF",
			"img": "25.png"
		},
		39: {
			"descr": "Изменения",
			"color": "#BF6F77",
			"img": "26.png"
		},
		40: {
			"descr": "Вкладка",
			"color": "#00FF00",
			"img": "27.png"
		},
		41: {
			"descr": "Пломбировка (чуж.)",
			"color": "#00FF00",
			"img": "42.png"
		},
		42: {
			"descr": "Временная пломба",
			"color": "#00A8FF",
			"img": "49.png"
		}
};


var surface_status_arr = {
		61: {
			"descr": "Здоровый",
			"color": "#FFF",
			"img": "38.png"
		},
		62: {
			"descr": "Чужая пломба",
			"color": "#00EF00",
			"img": "28.png"
		},
		63: {
			"descr": "Временная пломба",
			"color": "#00A8FF",
			"img": "29.png"
		},
		64: {
			"descr": "Пломба кариес",
			"color": "#FF6911",
			"img": "30.png"
		},
		65: {
			"descr": "Пломба",
			"color": "#0000FF",
			"img": "31.png"
		},
		66: {
			"descr": "Штифт",
			"color": "#FFFF00",
			"img": "32.png"
		},
		67: {
			"descr": "Удален",
			"color": "#000",
			"img": "33.png"
		},
		68: {
			"descr": "Удален корень",
			"color": "#973F00",
			"img": "34.png"
		},
		69: {
			"descr": "Коронка",
			"color": "#9B0088",
			"img": "35.png"
		},
		70: {
			"descr": "Фасетка",
			"color": "#FF00FF",
			"img": "36.png"
		},
		71: {
			"descr": "Кариес",
			"color": "#FF0000",
			"img": "37.png"
		},
		72: {
			"descr": "Клиновидный деф.",
			"color": "#9B0088",
			"img": "35.png"
		},
		73: {
			"descr": "Винир",
			"color": "#97C78F",
			"img": "42.png"
		},
		74: {
			"descr": "Пульпит",
			"color": "#CF77A7",
			"img": "43.png"
		},
		75: {
			"descr": "Периодонтит",
			"color": "#600000",
			"img": "44.png"
		},
		76: {
			"descr": "Герметизация",
			"color": "#97EFA7",
			"img": "47.png"
		}
};
jQuery(function ($) {

	class AjaxLoader {

		constructor(action) {

			this.loading = true;
			this.datas = null;
			this.listeners = [];

			// - Publics Methods
			this.addListener = function (l) {
				if (this.loading)
					this.listeners.push(l);
				else
					l.loaded(this.datas);
			};

			// - Private Methods
			const methodePrivee = function () {
			};

			const me = this;
			$.ajax({
				url: easqyeffectifs.ajaxurl,
				method: "GET",
				data: {
					action: action,
				},
				success: function (data) {

					if (data.success && data.data.status === 'ok') {
						me.datas = data.data;
					}
					else
						me.datas = null;
					//console.log(action, data, me);
				},
				error: (data) => {
					me.datas = null;
					console.error(action, "error", data);
				},

			}).always(function () {
				me.loading = false;
				if (me.datas)
					me.listeners.forEach(l => l.loaded(me.datas));
				me.listeners = [];
			});
		}
	}

	const Effectifs = (function () {

		let instance = null;
		return new function () {

			this.getInstance = function (listener) {

				if (instance == null) {
					instance = new AjaxLoader('easqy_effectifs');
					instance.constructeur = null;
				}

				instance.addListener(listener);
				return instance;
			}
		}

	})();

	const Renouvellements = (function () {

		let instance = null;
		return new function () {

			this.getInstance = function (listener) {

				if (instance == null) {
					instance = new AjaxLoader('easqy_renouvellements');
					instance.constructeur = null;
				}

				instance.addListener(listener);
				return instance;
			}
		}
	})();

	const EffectifsGeographiques = (function () {

		let instance = null;
		return new function () {

			this.getInstance = function (listener) {

				if (instance == null) {
					instance = new AjaxLoader('easqy_effectifs_geographiques');
					instance.constructeur = null;
				}

				instance.addListener(listener);
				return instance;
			}
		}

	})();

	class Chart {

		constructor(id) {
			this.id = id;
			this.datas = null;
		}

		loaded(datas) {
			this.datas = datas;
			if (this.datas)
				this.render();
		}

		render() { }
	}

	class CError extends Chart {

		constructor(id, message) {
			super(id);
			this.message = message;
		}

		render() {
			$(this.id).html(this.message);
		}
	}

	function defaultBarOptions() {
		return {
			chart: {
				type: 'bar',
				height: 350,
				animations: {
					enabled: true,
					speed: 200
				},
				dropShadow: {
					enabled: true,
					color: '#000',
					top: 9,
					left: 4,
					blur: 6,
					opacity: 0.5
				},
				toolbar: {
					show: false
				}
			},
			colors: ['#0d2366', 'var(--secondary-color)'],
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // top, center, bottom
					},
				}
			},
			dataLabels: {
				enabled: false,
			},
			xaxis: {
			},
			yaxis: {
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			title: {
				text: this.title,
				floating: false,
				align: 'center',
				style: {
					color: '#444'
				}
			},
			tooltip: {
				enabled: false,
			},
		};

	}

	class CEvolutionGlobale extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.effectifs = Effectifs.getInstance(this);
		}

		loaded(d) {
			const datas = d.effectifs;
			this.cumul = [];
			datas.years.forEach((y, i) => {
				this.cumul.push(0);
				datas.categories.effectifs.forEach(e => {
					this.cumul[this.cumul.length - 1] += e[2 * i + 0] + e[2 * i + 1];
				});
			});
			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const options = defaultBarOptions();
			options.series = [{ data: this.cumul }];
			options.dataLabels = {
				enabled: true,
				formatter: function (val) {
					return val + '';
				},
				offsetY: -20,
				style: {
					fontSize: '12px',
					colors: ["#444"]
				}
			};
			options.xaxis = { categories: this.datas.years };


			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	class CEvolutionHF extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.effectifs = Effectifs.getInstance(this);
		}

		loaded(d) {
			const datas = d.effectifs;

			this.hommeIndex = datas.order[0] == 'M' ? 0 : 1;
			this.femmeIndex = this.hommeIndex == 0 ? 1 : 0;
			this.cumulH = [];
			this.cumulF = [];

			datas.years.forEach((y, i) => {
				let h = 0;
				let f = 0;
				datas.categories.effectifs.forEach(e => {
					h += e[2 * i + this.hommeIndex];
					f += e[2 * i + this.femmeIndex];
				});
				this.cumulH.push(h);
				this.cumulF.push(f);
			});
			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const options = defaultBarOptions();
			options.series = [{ name: 'Hommes', data: this.cumulH }, { name: 'Femmes', data: this.cumulF }];
			options.colors = ['#0d2366', 'var(--secondary-color)'];
			options.dataLabels = {
				enabled: true,
				formatter: function (val) {
					return val + '';
				},
				offsetY: -20,
				style: {
					fontSize: '12px',
					colors: ["#444"]
				}
			};
			options.xaxis = { categories: this.datas.years };

			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	class CEvolutionHFPart extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.effectifs = Effectifs.getInstance(this);
		}

		loaded(d) {
			const datas = d.effectifs;

			this.hommeIndex = datas.order[0] == 'M' ? 0 : 1;
			this.femmeIndex = this.hommeIndex == 0 ? 1 : 0;
			this.prcH = [];
			this.prcF = [];

			datas.years.forEach((y, i) => {

				let h = 0;
				let f = 0;

				datas.categories.effectifs.forEach(e => {
					h += e[2 * i + this.hommeIndex];
					f += e[2 * i + this.femmeIndex];
				});

				this.prcH.push((100 * h) / (h + f));
				this.prcF.push((100 * f) / (h + f));
			});

			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const d_options = defaultBarOptions();
			const options = {
				series: [
					{
						name: "Hommes",
						data: this.prcH
					},
					{
						name: "Femmes",
						data: this.prcF
					}
				],
				chart: d_options.chart,
				colors: ['#0d2366', 'var(--secondary-color)'],
				dataLabels: {
					enabled: true,
					formatter: function (val) {
						return val.toFixed(1) + '%';
					},
				},
				stroke: {
					curve: 'smooth'
				},
				title: {
					text: this.title,
					align: 'left'
				},
				grid: {},
				markers: { size: 1 },
				xaxis: { categories: this.datas.years },
				yaxis: {
					labels: {
						formatter: (value) => { return value.toFixed(0) },
					}
				},
				tooltip: {
					enabled: false,
				},
			};
			options.chart.type = 'line';

			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	class CTauxRenouvellement extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.renouvellements = Renouvellements.getInstance(this);
		}

		loaded(d) {
			const datas = d.renouvellements;
			this.nouveaux = datas.categories.renouvellements.map(r => r[0] - r[1]);
			this.renouvellements = datas.categories.renouvellements.map(r => r[1]);
			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const me = this;
			const options = defaultBarOptions();
			options.chart.stacked = true;
			options.chart.dropShadow.enabled = false;

			options.series = [{ name: 'Renouvellements', data: this.renouvellements }, { name: 'Nouveaux adhérents', data: this.nouveaux }];
			options.colors = ['var(--secondary-color)', '#0d2366'];
			options.dataLabels = {
				enabled: true,
				offsetY: 5,
				formatter: function (val, { dataPointIndex, seriesIndex }) {
					const prcRenouvellements = (me.renouvellements[dataPointIndex] / (me.renouvellements[dataPointIndex] + me.nouveaux[dataPointIndex]));
					const prc = 100.0 * ((seriesIndex === 0) ? prcRenouvellements : (1.0 - prcRenouvellements));
					return val + ' (' + prc.toFixed(1) + "%)";
				},
				style: {
					fontSize: '12px',
					colors: ["#eee"]
				}
			};
			options.xaxis = { categories: this.datas.categories.names };


			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	class CGeographiques extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.effectifsGeographiques = EffectifsGeographiques.getInstance(this);
		}

		loaded(d) {
			const datas = d.effectifs;
			this.series = datas.map(e => e.effectif);
			this.labels = datas.map(e => e.ville);
			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const d_options = defaultBarOptions();
			const options = {
				series: this.series,
				chart: d_options.chart,
				labels: this.labels,
				dataLabels: {
					enabled: true,
					formatter: function (val) {
						return val.toFixed(1) + '%';
					},
				},
				title: {
					text: this.title,
					align: 'left'
				},
				theme: {
					monochrome: {
						enabled: true,
						color: '#0d2366'
					}
				}
			};
			options.chart.type = 'pie';
			options.chart.dropShadow.enabled = false;

			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	class CGeographiquesSQY extends Chart {

		constructor(id, title) {
			super(id);
			this.title = title;
			this.effectifsGeographiques = EffectifsGeographiques.getInstance(this);
		}

		loaded(d) {
			const datas = d.effectifs;

			this.series = [0];
			this.labels = ['SQY'];
			datas.forEach(e => {
				//console.log(e);
				if (e.inSQY === 1) {
					this.series[0] += e.effectif;
				}
				else {
					this.series.push(e.effectif);
					this.labels.push(e.ville);
				}
			});
			super.loaded(datas);
		}

		render() {
			if (!this.datas) {
				$(this.id).html('Chargement...');
				return;
			}

			const d_options = defaultBarOptions();
			const options = {
				series: this.series,
				chart: d_options.chart,
				labels: this.labels,
				dataLabels: {
					enabled: true,
					formatter: function (val) {
						return val.toFixed(1) + '%';
					},
				},
				title: {
					text: this.title,
					align: 'left'
				},
				theme: {
					monochrome: {
						enabled: true,
						color: '#0d2366'
					}
				}
			};
			options.chart.type = 'pie';
			options.chart.dropShadow.enabled = false;

			$(this.id).html('');
			const chart = new ApexCharts($(this.id)[0] /* equivalent to document.querySelector */, options);
			chart.render();
		}
	}

	const charts = [];

	easqyeffectifs.charts.forEach((c, i) => {

		const id = '[easqy-effectifs-index=' + (1 + i) + ']';
		let chart = null;
		switch (c.type) {
			case 'evolution_globale': chart = new CEvolutionGlobale(id, c.title); break;
			case 'evolution_hf': chart = new CEvolutionHF(id, c.title); break;
			case 'evolution_hf_part': chart = new CEvolutionHFPart(id, c.title); break;
			case 'renouvellement': chart = new CTauxRenouvellement(id, c.title); break;
			case 'geographiques': chart = new CGeographiques(id, c.title); break;
			case 'geographiques_sqy': chart = new CGeographiquesSQY(id, c.title); break;
			default: chart = new CError(id, 'Invalid type'); break;
		};

		charts.push(chart);
		chart.render();
	});

});



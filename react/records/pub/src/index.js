import {
	Component,
	render
} from '@wordpress/element';
import {
	SelectControl, Button, ButtonGroup
} from '@wordpress/components';
import './main.scss';

import $ from "jquery";

const slug = function (str) {

	if (!str) {
		console.error('str is not defined', str);
		return "-";
	}

	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	var from = "ÁÄÂÀÃÅČÇĆĎÉĚËÈÊẼĔȆĞÍÌÎÏİŇÑÓÖÒÔÕØŘŔŠŞŤÚŮÜÙÛÝŸŽáäâàãåčçćďéěëèêẽĕȇğíìîïıňñóöòôõøðřŕšşťúůüùûýÿžþÞĐđßÆa·/_,:;";
	var to = "AAAAAACCCDEEEEEEEEGIIIIINNOOOOOORRSSTUUUUUYYZaaaaaacccdeeeeeeeegiiiiinnooooooorrsstuuuuuyyzbBDdBAa------";
	for (var i = 0, l = from.length; i < l; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

	str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by -
		.replace(/-+/g, '-'); // collapse dashes

	return str;
};

const slugEpreuveFamily = (e) => 'epreuve-' + slug(e);

class Record extends Component {
	constructor(props) {
		super(props);
	};

	render() {
		const {
			record,
			categories,
			athletes,
			ra
		} = this.props;


		var dateParts = record.d.split('-');
		const recordDate = () => {
			const d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
			const options = { year: 'numeric', month: 'long', day: 'numeric' };
			return d.toLocaleDateString('fr-FR', options); //new Date(dateParts[2], dateParts[1] - 1, dateParts[0]); 
		}

		const Delta = () => {
			if (record.f) {
				return <span className={'delta'}>{record.f}</span>
			}
			return <></>
		}

		const currentDate = new Date();
		let classComp = 'record';
		if (parseInt(dateParts[0]) === currentDate.getFullYear())
			classComp = 'recordyear';

		const rAthletes = () => ra.filter(a => a.r === record.i).map(ra => {
			const at = athletes.find(a => a.i === ra.a);
			return <div className={'nom'}>
				{at.p}&nbsp;{at.n}
				{ra.c && (<span>({categories[ra.c]})</span>)}
			</div>
		});

		return (
			<div className={classComp}>
				<div className={'inner'}>
					{(!this.props.hideCategorie) &&
						<div className={'categorie'}>
							{categories[record.c]}
							{ /* record[iCAT_WHEN_PERF] ? ' (' + record[iCAT_WHEN_PERF] + ' )' : '' */}
						</div>
					}
					<div className={'athletes'}>{rAthletes()}</div>
					<div className={'perf'}>{record.p}<Delta /></div>
					<div className={'date'}>{recordDate()}</div>
					<div className={'lieu'}>{record.l}</div>
				</div>
			</div>
		)
	}
}

class Epreuve extends Component {
	constructor(props) {
		super(props);
	};

	render() {

		const {
			name, records, categories, athletes, ra
		} = this.props;

		return (
			<div className={'epreuve'}>
				<hr />
				<div className={'epreuve-name'}>
					{name}
					<div style={{ float: 'right' }}><a href='#easqy-records'><i class="fas fa-angle-up"></i></a></div>
				</div>
				<div className={'container'}>
					{
						records.map(r => {
							return (<Record
								key={r.i}
								record={r}
								categories={categories}
								athletes={athletes}
								ra={ra}
							/>)
						})
					}
				</div>
			</div>
		);
	}
}

class EpreuveFamily extends Component {
	constructor(props) {
		super(props);
	};

	render() {

		const {
			nom,
			categories,
			epreuves,
			athletes,
			ra
		} = this.props;

		const slug = slugEpreuveFamily(nom);

		const byEpreuve = {}
		this.props.records.forEach(r => {
			if (!byEpreuve[r.e])
				byEpreuve[r.e] = [];

			byEpreuve[r.e].push(r);
		})

		return (
			<div className={'epreuve-family'} id={slug}>
				<div className={'family-name'}>{nom}</div>
				<div>
					{Object.entries(byEpreuve).map(e => {
						const epreuveId = parseInt(e[0]);
						return (<Epreuve
							key={epreuveId}
							name={epreuves[epreuveId]}
							records={e[1]}
							categories={categories}
							athletes={athletes}
							ra={ra}
						/>)
					})}
				</div>
			</div>
		);
	}
}

class AthleteSelector extends Component {

	constructor(props) {
		super(props);
		this.state = {
			currentAthlete: this.props.currentAthlete
		};
	};
	render() {

		const oAthletes = this.props.athletes.map(a => {
			return { value: a.i, label: a.p + ' ' + a.n }
		});

		oAthletes.sort((o1, o2) => (o1.label > o2.label) ? 1 : -1);

		const props = {
			disabled: false,
			label: 'Athlètes',
			labelPosition: 'side',
			multiple: false,
			options: [{
				value: -1, label: ' - Tous les athletes -'
			}, ...oAthletes],
			size: 'default',
		};

		return <SelectControl
			{...props}
			value={this.state.currentAthlete}
			onChange={(a) => { this.props.onChange(a); this.setState({ currentAthlete: a }) }}
		/>
	}
}

class FilterEnvironement extends Component {
	constructor(props) {
		super(props);
	};

	render() {

		const { availableEnv, environement, environements } = this.props;

		return <ButtonGroup>
			{availableEnv.map(e =>
				<Button
					isPressed={e === environement}
					onClick={() => { this.props.onChange(e) }}
				>{environements[e]}</Button>
			)}
		</ButtonGroup>
	}
}

class FilterGenres extends Component {
	constructor(props) {
		super(props);
	};

	render() {

		const { availableGenres, genre, genres } = this.props;


		return <ButtonGroup>
			{availableGenres.map(g =>
				<Button
					isPressed={g === genre}
					onClick={() => { this.props.onChange(g) }}
				>{genres[g]}</Button>
			)}
		</ButtonGroup>
	}
}

class FilterEnvAndGenres extends Component {
	constructor(props) {
		super(props);
	};

	render() {

		const {
			athleteRecords,
			genre, genres,
			environement, environements
		} = this.props;

		const availableEnv = [];
		athleteRecords.forEach(r => {
			if (availableEnv.indexOf(r.en) < 0)
				availableEnv.push(r.en);
		});

		const availableGenres = [];
		athleteRecords.forEach(r => {
			if ((r.en === environement) && (availableGenres.indexOf(r.g) < 0))
				availableGenres.push(r.g);
		});
		if (availableGenres.indexOf(genre) < 0) {
			this.props.onChange({ genre: availableGenres[0] })
			return <></>
		}

		return <div style={{ display: 'flex', justifyContent: 'space-evenly' }}>
			{(availableEnv.length > 1) &&
				<FilterEnvironement
					key={environement}
					environement={environement}
					environements={environements}
					availableEnv={availableEnv}
					onChange={(e) => { this.props.onChange({ environement: e }) }}
				/>
			}
			{(availableGenres.length > 1) &&
				<FilterGenres
					key={genre}
					genre={genre}
					genres={genres}
					availableGenres={availableGenres}
					onChange={(g) => { this.props.onChange({ genre: g }) }}
				/>
			}
		</div>
	}
}

class Records extends Component {
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
			currentAthlete: -1,
			environement: 0,
			genre: -1,
			famille: -1
		};
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "GET",
			data: {
				action: "easqy_records",
			},
			success: function (data) {
				if (data.data.status === 'ok') {
					me.datas = data.data;

					const availableGenres = [];
					me.datas.records.forEach(r => {
						if (availableGenres.indexOf(r.g) < 0)
							availableGenres.push(r.g);
					});
					me.availableGenres = availableGenres;

					me.setState({
						loading: false,
						genre: availableGenres.length ? availableGenres[0] : -1
					});
				}
				else
					me.setState({ loading: false });
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ loading: false });
			},
		});
	}

	render() {
		const { loading } = this.state;

		if (loading || (!this.datas))
			return <></>;

		const {
			genres,
			categories,
			environements,
			epreuves,
			familles,
			athletes,
			records,
			ra
		} = this.datas;


		const { environement, genre } = this.state;
		const raFiltered = (this.state.currentAthlete === -1) ? ra : ra.filter(ra => ra.a === this.state.currentAthlete);
		const athleteRecords = records.filter(r => {
			return raFiltered.findIndex(ra => ra.r === r.i) >= 0
		});

		const filteredRecords = athleteRecords.filter(r => (r.en === environement) && (r.g === genre));

		const availableFamilies = [];
		filteredRecords.forEach(r => {
			if (availableFamilies.indexOf(r.fa) < 0)
				availableFamilies.push(r.fa);
		});
		if ((availableFamilies.length > 0) && (availableFamilies.indexOf(this.state.famille) < 0)) {
			this.setState({ famille: availableFamilies[0] })
			return <></>
		}

		const Filters = () => {
			return (
				<>
					<AthleteSelector
						key={this.state.currentAthlete}
						athletes={athletes}
						currentAthlete={this.state.currentAthlete}
						onChange={(a) => { this.setState({ currentAthlete: parseInt(a) }) }}
					/>
					<FilterEnvAndGenres
						athleteRecords={athleteRecords}
						genres={genres}
						genre={genre}
						environements={environements}
						environement={environement}
						onChange={(c) => { this.setState(c) }}
					/>
					<div>&nbsp;</div>
					<ButtonGroup style={{ display: 'flex', justifyContent: 'center' }}>
						{(availableFamilies.length > 1) &&
							availableFamilies.map(f => {
								return <Button key={f} href={'#' + slugEpreuveFamily(familles[f])}>{familles[f]}</Button>
							})
						}
					</ButtonGroup>
				</>
			)
		};

		return <div className={'records'}>
			<Filters />
			{
				availableFamilies.map(famille => {
					return <EpreuveFamily
						key={famille}
						nom={familles[famille]}
						records={filteredRecords.filter(rec => rec.fa === famille)}
						categories={categories}
						epreuves={epreuves}
						athletes={athletes}
						ra={ra}
					/>
				})
			}
		</div>;
	}
}

class AllRecords extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading: true,
			currentAthlete: -1,
			indoor: 0,
			genre: -1,
			famille: -1
		};
		this.datas = {};
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "GET",
			data: {
				action: "easqy_records",
			},
			success: function (data) {
				if (data.data.status === 'ok') {
					me.datas = data.data;

					const availableGenres = [];
					me.datas.records.forEach(r => {
						if (availableGenres.indexOf(r.g) < 0)
							availableGenres.push(r.g);
					});
					me.availableGenres = availableGenres;

					me.setState({
						loading: false,
						genre: availableGenres.length ? availableGenres[0] : -1
					});
				}
				else
					me.setState({ loading: false });
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ loading: false });
			},
		});
	}

	render() {

		if (this.state.loading)
			return <></>;

		console.log(this.datas);

		const {
			athletes,
			categories,
			epreuves,
			records,
			ra
		} = this.datas;

		const rows = [];

		epreuves.forEach((e, iE) => {

			const cols = [];
			let count = 0;
			categories.forEach((c, iC) => {

				const r = records.find(r => (r.e === iE) && (r.c === iC));
				if (r) {
					cols.push(<td>
						<Record
							hideCategorie={true}
							record={r}
							athletes={athletes}
							categories={categories}
							ra={ra}
						/></td>); count++;
				}
				else cols.push(<td>&nbsp;-&nbsp;</td>)
			})

			if (count !== 0)
				rows.push(<tr><td>{e}</td>{cols} </tr>)
		})

		return <div className={'records'}>
			<table>
				{rows}
			</table>
		</div>
	}

}

(function () {
	const elt = document.getElementById('easqy-records');
	if (elt)
		render(<Records />, elt);
})();

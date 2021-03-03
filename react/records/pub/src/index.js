import {
	Component,
	useState,
	render
} from '@wordpress/element';

import {
	SelectControl, Button, ButtonGroup
} from '@wordpress/components';

//import Wind from './wind.svg'
import './main.scss';

import $ from "jquery";


var slug = function (str) {
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
			return <div className={'nom'}>{at.p}&nbsp;{at.n}</div>
		});

		return (
			<div className={classComp}>
				<div className={'inner'}>
					<div className={'categorie'}>
						{categories[record.c]}
						{ /* record[iCAT_WHEN_PERF] ? ' (' + record[iCAT_WHEN_PERF] + ' )' : '' */}
					</div>
					{rAthletes()}
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

class Records extends Component {
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
			indoor: 0,
			genre: -1,
			famille: -1
		};
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "POST",
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
			epreuves,
			familles,
			athletes,
			records,
			ra
		} = this.datas;

		const { indoor, genre } = this.state;

		const frecords = records.filter(r => (r.in === indoor) && (r.g === genre));

		const availableFamilies = [];
		records.forEach(r => {
			if (availableFamilies.indexOf(r.fa) < 0)
				availableFamilies.push(r.fa);
		});

		const EpreuveFamilyQuicAccess = () => {
			return (
				<>
					<ButtonGroup>
						<Button
							isPressed={indoor === 1}
							onClick={() => { this.setState({ indoor: 1 }) }}
						>Indoor</Button>
						<Button
							isPressed={indoor === 0}
							onClick={() => { this.setState({ indoor: 0 }) }}
						>Outdoor</Button>

						<span>&nbsp;|&nbsp;</span>
						{
							this.availableGenres.map(g =>
								<Button
									isPressed={g === genre}
									onClick={() => { this.setState({ genre: g }) }}
								>{genres[g]}</Button>
							)
						}
					</ButtonGroup>
					<div>&nbsp;</div>
					<ButtonGroup>
						{
							availableFamilies.map(f => {
								return <Button key={f} href={'#' + slugEpreuveFamily(familles[f])}>{familles[f]}</Button>
							})
						}
					</ButtonGroup>
				</>
			)
		};

		return <div className={'records'}>
			<h2>Les records du club</h2>
			<EpreuveFamilyQuicAccess />
			{
				availableFamilies.map(famille => {
					return <EpreuveFamily
						key={famille}
						nom={familles[famille]}
						records={frecords.filter(r => r.fa === famille)}
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

// Render the app inside our shortcode's #app div
render(
	<Records />,
	document.getElementById('easqy-records')
);

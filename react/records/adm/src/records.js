
import { Component } from '@wordpress/element';
import CompetitionRecords from './competitions_records';
import $ from "jquery";
import EditRecord from './edit_record';

const ViewType = {
	loading: 0,
	error: 1,
	competitions: 2,
	add: 3,
	modify: 4
};
Object.freeze(ViewType);

export default class Records extends Component {

	constructor(props) {
		super(props);
		this.state = {
			viewType: ViewType.loading,
		};

		this.props = { ...this.props, records: [], athletes: [], categories: [], genres: [], epreuves: [], ra: [] }
	}

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_records",
			},
			success: function (data) {
				const { records, athletes, ra, categories, genres, epreuves } = data.data;
				console.log(data.data);

				me.props.categories = categories;
				me.props.epreuves = epreuves;
				me.props.genres = genres;
				me.props.athletes = athletes;
				me.props.records = records;
				me.props.ra = ra;

				me.setState({
					viewType: ViewType.competitions
				});
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	doDelRecord(rId) {
		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_record_del",
				recordId: rId
			},
			success: function (data) {
				console.log('record id ', rId, 'deleted');
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	render() {

		const {
			categories,
			epreuves,
			genres,
			records,
			ra,
			athletes
		} = this.props;

		const {
			viewType
		} = this.state;

		const header = () => (
			<div>
				<h1 class="wp-heading-inline">Records</h1>&nbsp;
				{(viewType === ViewType.competitions) && (
					<a
						href="#"
						className={"page-title-action"}
						onClick={(e) => { this.setState({ viewType: ViewType.add }) }}
					>
						Ajouter
					</a>
				)}
			</div>
		);

		const competitionRecords = () => (
			<CompetitionRecords
				categories={categories}
				epreuves={epreuves}
				genres={genres}
				athletes={athletes}
				records={records}
				ra={ra}
				doDelRecord={(rId) => { this.doDelRecord(rId) }}
			/>);

		const addRecord = () => (
			<EditRecord
				createMode={true}
				categories={categories}
				epreuves={epreuves}
				genres={genres}
				athletes={athletes}
				records={records}
				ra={ra}
				onAthletesChanged={() => { }}
				onCancel={() => { this.setState({ viewType: ViewType.competitions }) }}
			/>);

		return (
			<div id="easqy-records-adm" className="wrap">
				{header()}
				{(viewType === ViewType.competitions) && (competitionRecords())}
				{(viewType === ViewType.add) && (addRecord())}
			</div>
		)
	}
}

import Foundation

// The response model stays separate from the locally persisted Core Data entity.
struct AnnouncementDTO: Codable {
    let title: String
    let date: String
    let category: String
    let location: String?
    let priority: String?

    private enum CodingKeys: String, CodingKey {
        case title, date, category, location, priority, priorityLevel
        case prioritySnakeCase = "priority_level"
    }

    init(from decoder: Decoder) throws {
        let values = try decoder.container(keyedBy: CodingKeys.self)
        title = try values.decode(String.self, forKey: .title)
        date = try values.decode(String.self, forKey: .date)
        category = try values.decode(String.self, forKey: .category)
        location = try values.decodeIfPresent(String.self, forKey: .location)
        priority = try values.decodeIfPresent(String.self, forKey: .priority)
            ?? values.decodeIfPresent(String.self, forKey: .priorityLevel)
            ?? values.decodeIfPresent(String.self, forKey: .prioritySnakeCase)
    }

    func encode(to encoder: Encoder) throws {
        var values = encoder.container(keyedBy: CodingKeys.self)
        try values.encode(title, forKey: .title)
        try values.encode(date, forKey: .date)
        try values.encode(category, forKey: .category)
        try values.encodeIfPresent(location, forKey: .location)
        try values.encodeIfPresent(priority, forKey: .priority)
    }
}
